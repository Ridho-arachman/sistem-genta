<?php

namespace App\Http\Controllers;

use App\Models\SpWarning;
use App\Models\User;
use App\Services\CsvExport;
use App\Services\FcmService;
use Illuminate\Http\Request;

class SpWarningController extends Controller
{
    public function __construct(protected FcmService $fcm) {}

    public function index(Request $request)
    {
        $query = SpWarning::with(['user', 'admin'])->latest();

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->filled('level')) {
            $query->where('level', $request->level);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('start_date')) {
            $query->whereDate('issued_at', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->whereDate('issued_at', '<=', $request->end_date);
        }

        $warnings = $query->paginate(15)->withQueryString();
        $pengawas = User::where('role', 'pengawas')->get();

        return view('admin.sp-warnings.index', compact('warnings', 'pengawas'));
    }

    public function create()
    {
        $pengawas = User::where('role', 'pengawas')->get();

        return view('admin.sp-warnings.create', compact('pengawas'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => ['required', 'exists:users,id'],
            'level' => ['required', 'in:1,2,3'],
            'reason' => ['required', 'string'],
        ]);

        $warning = SpWarning::create([
            'user_id' => $validated['user_id'],
            'admin_id' => auth()->id(),
            'level' => $validated['level'],
            'reason' => $validated['reason'],
            'status' => 'aktif',
        ]);

        // Kirim notifikasi ke pengawas
        $user = User::find($validated['user_id']);
        if ($user) {
            $this->sendSpNotification($user, $warning);

            // Kirim juga ke manajerial
            $this->notifyManajerial($warning);
        }

        return redirect()->route('admin.sp-warnings.index')->with('status', 'SP berhasil diterbitkan.');
    }

    public function show(SpWarning $spWarning)
    {
        $spWarning->load(['user', 'admin']);

        return view('admin.sp-warnings.show', compact('spWarning'));
    }

    public function update(Request $request, SpWarning $spWarning)
    {
        $validated = $request->validate([
            'status' => ['required', 'in:aktif,dicabut,selesai'],
        ]);

        $spWarning->update($validated);

        return redirect()->route('admin.sp-warnings.index')->with('status', 'SP berhasil diperbarui.');
    }

    public function destroy(SpWarning $spWarning)
    {
        $spWarning->delete();

        return redirect()->route('admin.sp-warnings.index')->with('status', 'SP berhasil dihapus.');
    }

    public function export(Request $request)
    {
        $query = SpWarning::with(['user', 'admin'])->latest();

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->filled('level')) {
            $query->where('level', $request->level);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('start_date')) {
            $query->whereDate('issued_at', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->whereDate('issued_at', '<=', $request->end_date);
        }

        $warnings = $query->get();
        $filename = 'laporan-sp-'.now()->format('Ymd_His');

        $format = $request->get('format', 'csv');

        // "xlsx" & "pdf": gunakan view print (format browser-native), bukan HTML kustom yang berantakan.
        if ($format === 'xlsx' || $format === 'pdf') {
            return view('admin.sp-warnings.print', [
                'warnings' => $warnings,
                'startDate' => $request->start_date,
                'endDate' => $request->end_date,
            ]);
        }

        // CSV
        $csv = CsvExport::csv(
            ['Tanggal', 'Pengawas', 'Level', 'Alasan', 'Status', 'Diterbitkan Oleh'],
            $warnings->map(fn ($w) => [
                $w->issued_at->format('Y-m-d H:i:s'),
                $w->user->name,
                'SP-'.$w->level,
                $w->reason,
                $w->status,
                $w->admin->name,
            ])
        );

        return response($csv)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', "attachment; filename=\"{$filename}.csv\"");
    }

    private function sendSpNotification(User $user, SpWarning $warning)
    {
        $levelLabels = [
            1 => 'SP-1 (Peringatan Pertama)',
            2 => 'SP-2 (Peringatan Kedua)',
            3 => 'SP-3 (Peringatan Terakhir/PHK)',
        ];

        $this->fcm->sendToUser($user->id, [
            'title' => '⚠️ Surat Peringatan Baru',
            'body' => "Anda menerima {$levelLabels[$warning->level]}: {$warning->reason}",
            'icon' => 'ic_stat_icon_config_sample',
            'color' => $warning->level === 3 ? '#ef4444' : '#f59e0b',
            'sound' => 'default',
            'click_action' => 'FLUTTER_NOTIFICATION_CLICK',
        ], [
            'type' => 'sp_warning',
            'level' => $warning->level,
            'reason' => $warning->reason,
            'action_url' => "/sp-warnings/{$warning->id}",
        ]);
    }

    private function notifyManajerial(SpWarning $warning)
    {
        $manajerials = User::where('role', 'manajerial')->get();

        foreach ($manajerials as $m) {
            $this->fcm->sendToUser($m->id, [
                'title' => '📋 Surat Peringatan Diterbitkan',
                'body' => "Pengawas {$warning->user->name} menerima SP-{$warning->level}",
                'icon' => 'ic_stat_icon_config_sample',
                'color' => '#22c55e',
                'sound' => 'default',
                'click_action' => 'FLUTTER_NOTIFICATION_CLICK',
            ], [
                'type' => 'sp_warning_notify',
                'pengawas_id' => $warning->user_id,
                'level' => $warning->level,
                'action_url' => "/sp-warnings/{$warning->id}",
            ]);
        }
    }
}
