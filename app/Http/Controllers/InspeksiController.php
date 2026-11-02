<?php

namespace App\Http\Controllers;

use App\Exports\AbsensiXlsxExport;
use App\Jobs\AbsensiReminderJob;
use App\Models\Absensi;
use App\Models\Dapur;
use App\Services\GeolocationService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;

class InspeksiController extends Controller
{
    use AuthorizesRequests;

    public function __construct(
        protected GeolocationService $geolocation
    ) {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $this->authorize('viewAny', Absensi::class);

        $user = auth()->user();

        // Pengawas hanya lihat inspeksi sendiri
        if ($user->isPengawas()) {
            $query = Absensi::where('user_id', $user->id)->with('dapur');
        } else {
            // Admin & Manajerial lihat semua
            $query = Absensi::with('user', 'dapur');
        }

        if ($request->filled('dapur_id')) {
            $query->where('dapur_id', $request->dapur_id);
        }

        if ($request->filled('start_date')) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }

        $inspeksis = $query->latest()->paginate(15)->withQueryString();
        $dapurs = Dapur::all();

        if ($user->isPengawas()) {
            return view('pengawas.inspeksi.index', compact('inspeksis', 'dapurs'));
        }

        $prefix = $user->isAdmin() ? 'admin' : 'manajerial';

        return view('absensis.index', compact('inspeksis', 'dapurs'))
            ->with('indexRoute', "{$prefix}.inspeksi.index")
            ->with('createRoute', "{$prefix}.inspeksi.create")
            ->with('exportRoute', "{$prefix}.inspeksi.export");
    }

    public function create()
    {
        $this->authorize('create', Absensi::class);
        $dapurs = Dapur::all();

        return view('pengawas.inspeksi.create', compact('dapurs'));
    }

    public function store(Request $request)
    {
        $this->authorize('create', Absensi::class);

        $user = auth()->user();
        $isAdminCreatingForPengawas = $user->isAdmin() && $request->filled('user_id');

        $validated = $request->validate([
            'dapur_id' => ['required', 'exists:dapurs,id'],
            'lat' => ['required', 'numeric', 'between:-90,90'],
            'lng' => ['required', 'numeric', 'between:-180,180'],
            'selfie_path' => ['required', 'image', 'max:2048'],
            'status' => ['required', 'in:hadir,izin,cuti,tidak_hadir'],
        ]);

        if ($request->hasFile('selfie_path')) {
            $validated['selfie_path'] = $request->file('selfie_path')->store('selfies', 'public');
        }

        // Allow admin to create inspeksi for specific pengawas
        if ($isAdminCreatingForPengawas) {
            $validated['user_id'] = $request->user_id;
        } else {
            $validated['user_id'] = auth()->id();
        }
        $validated['check_in_at'] = now();

        $dapur = Dapur::find($validated['dapur_id']);
        if ($dapur && $dapur->latitude && $dapur->longitude) {
            $distance = $this->geolocation->distance(
                $validated['lat'], $validated['lng'],
                (float) $dapur->latitude, (float) $dapur->longitude
            );

            if ($distance > ($dapur->geofence_radius ?? 100)) {
                return back()->withErrors([
                    'lokasi' => 'Anda berada di luar radius dapur (jarak: '.round($distance).'m, radius: '.($dapur->geofence_radius ?? 100).'m). Inspeksi dibatalkan.',
                ])->withInput();
            }
        }

        $inspeksi = Absensi::create($validated);

        AbsensiReminderJob::dispatch(
            $validated['user_id'],
            'checkout_reminder',
            $validated['dapur_id'],
            '17:00'
        )->delay(now()->addHours(8));

        $redirectRoute = $user->isPengawas() ? 'pengawas.inspeksi.index' : ($user->isAdmin() ? 'admin.inspeksi.index' : 'manajerial.inspeksi.index');

        return redirect()->route($redirectRoute)->with('status', 'Inspeksi check-in berhasil.');
    }

    public function checkOut(Request $request, Absensi $inspeksi)
    {
        $this->authorize('checkOut', $inspeksi);

        $inspeksi->update(['check_out_at' => now()]);

        return back()->with('status', 'Check-out berhasil dicatat.');
    }

    public function export(Request $request)
    {
        $this->authorize('export', Absensi::class);

        $format = $request->get('format', 'csv');

        $query = Absensi::with('user', 'dapur');

        if ($request->filled('dapur_id')) {
            $query->where('dapur_id', $request->dapur_id);
        }

        if ($request->filled('start_date')) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }

        $inspeksis = $query->latest()->get();
        $filename = 'laporan-inspeksi-'.now()->format('Ymd_His');

        if ($format === 'xlsx') {
            $exporter = new AbsensiXlsxExport(
                $request->start_date,
                $request->end_date,
                $request->dapur_id
            );

            return response($exporter->generate())
                ->header('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet')
                ->header('Content-Disposition', "attachment; filename=\"{$filename}.xlsx\"");
        }

        if ($format === 'pdf') {
            return view('absensis.print', [
                'inspeksis' => $inspeksis,
                'startDate' => $request->start_date,
                'endDate' => $request->end_date,
                'dapurId' => $request->dapur_id,
                'filename' => $filename,
            ]);
        }

        // Default: CSV
        $csv = "Tanggal,Pengawas,Dapur,Check-in,Check-out,Status,Lat,Lng,Foto\n";
        foreach ($inspeksis as $i) {
            $csv .= sprintf(
                "%s,%s,%s,%s,%s,%s,%s,%s,%s\n",
                $i->created_at->format('Y-m-d H:i:s'),
                $i->user->name,
                $i->dapur->nama,
                $i->check_in_at?->format('Y-m-d H:i:s') ?? '-',
                $i->check_out_at?->format('Y-m-d H:i:s') ?? '-',
                $i->status,
                $i->lat ?? '-',
                $i->lng ?? '-',
                $i->selfie_path ? asset('storage/'.$i->selfie_path) : '-'
            );
        }

        return response($csv)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', "attachment; filename=\"{$filename}.csv\"");
    }
}
