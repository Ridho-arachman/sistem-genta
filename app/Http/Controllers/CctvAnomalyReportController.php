<?php

namespace App\Http\Controllers;

use App\Models\Cctv;
use App\Models\CctvAnomalyReport;
use App\Models\User;
use App\Services\FcmService;
use Illuminate\Http\Request;

class CctvAnomalyReportController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth']);
    }

    // Admin: Create anomaly report
    public function create()
    {
        $this->authorize('admin');
        $cctvs = Cctv::with('dapur')->get();

        return view('admin.cctv-anomalies.create', compact('cctvs'));
    }

    public function store(Request $request)
    {
        $this->authorize('admin');

        $validated = $request->validate([
            'cctv_id' => ['required', 'exists:cctvs,id'],
            'description' => ['required', 'string'],
            'severity' => ['required', 'in:rendah,sedang,tinggi'],
        ]);

        $anomaly = CctvAnomalyReport::create([
            'cctv_id' => $validated['cctv_id'],
            'admin_id' => auth()->id(),
            'description' => $validated['description'],
            'severity' => $validated['severity'],
            'status' => 'baru',
        ]);

        // Notify manajerial
        $this->notifyManajerial($anomaly);

        return redirect()->route('admin.cctv-anomalies.index')->with('status', 'Laporan anomali CCTV berhasil dibuat.');
    }

    // All roles (read-only for manajerial/kepala_dapur)
    public function index(Request $request)
    {
        $user = auth()->user();
        $query = CctvAnomalyReport::with(['cctv.dapur', 'admin'])->latest();

        // Filter by dapur for kepala dapur
        if ($user->isKepalaDapur()) {
            // Assuming kepala dapur is assigned to specific dapur(s)
            // For now, show all but can be filtered
        }

        if ($request->filled('cctv_id')) {
            $query->where('cctv_id', $request->cctv_id);
        }

        if ($request->filled('severity')) {
            $query->where('severity', $request->severity);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('start_date')) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }

        $anomalies = $query->paginate(15)->withQueryString();
        $cctvs = Cctv::with('dapur')->get();

        if ($user->isAdmin()) {
            return view('admin.cctv-anomalies.index', compact('anomalies', 'cctvs'));
        } elseif ($user->isManajerial()) {
            return view('manajerial.cctv-anomalies.index', compact('anomalies', 'cctvs'));
        } elseif ($user->isKepalaDapur()) {
            return view('kepala-dapur.cctv-anomalies.index', compact('anomalies', 'cctvs'));
        }

        abort(403);
    }

    public function show(CctvAnomalyReport $anomaly)
    {
        $anomaly->load(['cctv.dapur', 'admin']);
        $user = auth()->user();

        if ($user->isAdmin()) {
            return view('admin.cctv-anomalies.show', compact('anomaly'));
        } elseif ($user->isManajerial()) {
            return view('manajerial.cctv-anomalies.show', compact('anomaly'));
        } elseif ($user->isKepalaDapur()) {
            return view('kepala-dapur.cctv-anomalies.show', compact('anomaly'));
        }

        abort(403);
    }

    // Admin only: Update status
    public function update(Request $request, CctvAnomalyReport $anomaly)
    {
        $this->authorize('admin');

        $validated = $request->validate([
            'status' => ['required', 'in:baru,diproses,selesai'],
        ]);

        $anomaly->update($validated);

        return redirect()->back()->with('status', 'Status anomali diperbarui.');
    }

    // Export for admin/manajerial
    public function export(Request $request)
    {
        $user = auth()->user();

        if (! $user->hasRole(['admin', 'manajerial'])) {
            abort(403);
        }

        $query = CctvAnomalyReport::with(['cctv.dapur', 'admin'])->latest();

        if ($request->filled('cctv_id')) {
            $query->where('cctv_id', $request->cctv_id);
        }

        if ($request->filled('severity')) {
            $query->where('severity', $request->severity);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('start_date')) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }

        $anomalies = $query->get();
        $filename = 'laporan-anomali-cctv-'.now()->format('Ymd_His');
        $format = $request->get('format', 'csv');

        if ($format === 'pdf') {
            return view('admin.cctv-anomalies.print', [
                'anomalies' => $anomalies,
                'startDate' => $request->start_date,
                'endDate' => $request->end_date,
                'filename' => $filename,
            ]);
        }

        // CSV / XLSX
        $csv = "Tanggal,CCTV,Dapur,Area/Kamera,Deskripsi,Severity,Status,Dilaporkan Oleh\n";
        foreach ($anomalies as $a) {
            $csv .= sprintf(
                "%s,%s,%s,%s,%s,%s,%s,%s\n",
                $a->created_at->format('Y-m-d H:i:s'),
                $a->cctv->nama,
                $a->cctv->dapur->nama,
                $a->cctv->section ?? '-',
                str_replace("\n", ' ', $a->description),
                ucfirst($a->severity),
                ucfirst($a->status),
                $a->admin->name
            );
        }

        return response($csv)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', "attachment; filename=\"{$filename}.csv\"");
    }

    private function authorize(string $role): void
    {
        if (! auth()->user()->hasRole($role)) {
            abort(403);
        }
    }

    private function notifyManajerial(CctvAnomalyReport $anomaly)
    {
        $manajerials = User::where('role', 'manajerial')->get();

        foreach ($manajerials as $m) {
            app(FcmService::class)->sendToUser($m->id, [
                'title' => '🚨 Anomali CCTV Terdeteksi',
                'body' => "CCTV {$anomaly->cctv->display_name} di {$anomaly->cctv->dapur->nama}: {$anomaly->description}",
                'icon' => 'ic_stat_icon_config_sample',
                'color' => $anomaly->severity === 'tinggi' ? '#ef4444' : '#f59e0b',
                'sound' => 'default',
                'click_action' => 'FLUTTER_NOTIFICATION_CLICK',
            ], [
                'type' => 'cctv_anomaly',
                'anomaly_id' => $anomaly->id,
                'cctv_id' => $anomaly->cctv_id,
                'severity' => $anomaly->severity,
                'action_url' => "/cctv-anomalies/{$anomaly->id}",
            ]);
        }
    }
}
