<?php

namespace App\Http\Controllers;

use App\Models\Cctv;
use App\Models\CctvAnomalyReport;
use App\Models\User;
use App\Services\CsvExport;
use App\Services\FcmService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CctvAnomalyReportController extends Controller
{
    public function __construct(protected FcmService $fcm) {}

    // Admin: Create anomaly report
    public function create(Request $request)
    {
        $this->authorize('create', CctvAnomalyReport::class);
        $cctvs = Cctv::with('dapur')->get()->map(fn (Cctv $cctv) => [
            'id' => (string) $cctv->id,
            'label' => $cctv->display_name,
            'dapur_name' => $cctv->dapur->nama,
        ])->values();

        return view('admin.cctv-anomalies.create', [
            'cctvs' => $cctvs,
            'cctvId' => $request->query('cctv_id'),
        ]);
    }

    public function store(Request $request)
    {
        $this->authorize('create', CctvAnomalyReport::class);

        $validated = $request->validate([
            'cctv_id' => ['required', 'exists:cctvs,id'],
            'description' => ['required', 'string'],
            'severity' => ['required', 'in:rendah,sedang,tinggi'],
        ]);

        return DB::transaction(function () use ($validated) {
            // Idempotensi: cegah laporan anomali ganda untuk CCTV+deskripsi+
            // severity yang sama pada hari yang sama (double-click / retry admin).
            $existing = CctvAnomalyReport::where('cctv_id', $validated['cctv_id'])
                ->where('admin_id', auth()->id())
                ->where('description', $validated['description'])
                ->where('severity', $validated['severity'])
                ->where('status', 'baru')
                ->whereDate('created_at', today())
                ->lockForUpdate()
                ->first();

            if ($existing) {
                return redirect()->route('admin.cctv-anomalies.index')
                    ->with('status', 'Laporan anomali untuk kamera ini sudah tersimpan hari ini.');
            }

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
        });
    }

    // All roles (read-only for manajerial/kepala_dapur)
    public function index(Request $request)
    {
        $this->authorize('viewAny', CctvAnomalyReport::class);

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

        $anomalies = $query->paginate(15)->withQueryString();
        $cctvs = Cctv::with('dapur')->get();

        return view('admin.cctv-anomalies.index', compact('anomalies', 'cctvs'));
    }

    public function show(string $cctv_anomaly)
    {
        $anomaly = CctvAnomalyReport::with(['cctv.dapur', 'admin'])->findOrFail($cctv_anomaly);

        $this->authorize('view', $anomaly);

        return view('admin.cctv-anomalies.show', compact('anomaly'));
    }

    // Admin only: Update status
    public function update(Request $request, CctvAnomalyReport $anomaly)
    {
        $this->authorize('update', $anomaly);

        $validated = $request->validate([
            'status' => ['required', 'in:baru,diproses,selesai'],
        ]);

        $anomaly->update($validated);

        return redirect()->back()->with('status', 'Status anomali diperbarui.');
    }

    // Export for admin/manajerial
    public function export(Request $request)
    {
        $this->authorize('export', CctvAnomalyReport::class);

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

        // CSV
        $csv = CsvExport::csv(
            ['Tanggal', 'CCTV', 'Dapur', 'Area/Kamera', 'Deskripsi', 'Severity', 'Status', 'Dilaporkan Oleh'],
            $anomalies->map(fn ($a) => [
                $a->created_at->format('Y-m-d H:i:s'),
                $a->cctv->nama,
                $a->cctv->dapur->nama,
                $a->cctv->section ?? '-',
                $a->description,
                ucfirst($a->severity),
                ucfirst($a->status),
                $a->admin->name,
            ])
        );

        return response($csv)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', "attachment; filename=\"{$filename}.csv\"");
    }

    private function notifyManajerial(CctvAnomalyReport $anomaly)
    {
        $manajerials = User::where('role', 'manajerial')->get();

        foreach ($manajerials as $m) {
            $this->fcm->sendToUser($m->id, [
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
