<?php

namespace App\Http\Controllers;

use App\Exports\AbsensiXlsxExport;
use App\Jobs\AbsensiReminderJob;
use App\Models\Absensi;
use App\Models\Dapur;
use App\Services\GeolocationService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;

class AbsensiController extends Controller
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

        // Pengawas hanya lihat absensi sendiri
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

        $absensis = $query->latest()->paginate(15)->withQueryString();
        $dapurs = Dapur::all();

        if ($user->isPengawas()) {
            return view('pengawas.absensi.index', compact('absensis', 'dapurs'));
        }

        $prefix = $user->isAdmin() ? 'admin' : 'manajerial';

        return view('absensis.index', compact('absensis', 'dapurs'))
            ->with('indexRoute', "{$prefix}.absensi.index")
            ->with('createRoute', "{$prefix}.absensi.create")
            ->with('exportRoute', "{$prefix}.absensi.export");
    }

    public function create()
    {
        $this->authorize('create', Absensi::class);
        $dapurs = Dapur::all();

        return view('pengawas.absensi.create', compact('dapurs'));
    }

    public function store(Request $request)
    {
        $this->authorize('create', Absensi::class);

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

        $validated['user_id'] = auth()->id();
        $validated['check_in_at'] = now();

        $dapur = Dapur::find($validated['dapur_id']);
        if ($dapur && $dapur->latitude && $dapur->longitude) {
            $distance = $this->geolocation->distance(
                $validated['lat'], $validated['lng'],
                (float) $dapur->latitude, (float) $dapur->longitude
            );

            if ($distance > ($dapur->geofence_radius ?? 100)) {
                return back()->withErrors([
                    'lokasi' => 'Anda berada di luar radius dapur (jarak: '.round($distance).'m, radius: '.($dapur->geofence_radius ?? 100).'m). Absensi dibatalkan.',
                ])->withInput();
            }
        }

        $absensi = Absensi::create($validated);

        AbsensiReminderJob::dispatch(
            auth()->id(),
            'checkout_reminder',
            $validated['dapur_id'],
            '17:00'
        )->delay(now()->addHours(8));

        $user = auth()->user();
        $redirectRoute = $user->isPengawas() ? 'pengawas.absensi.index' : ($user->isAdmin() ? 'admin.absensi.index' : 'manajerial.absensi.index');

        return redirect()->route($redirectRoute)->with('status', 'Absensi check-in berhasil.');
    }

    public function checkOut(Request $request, Absensi $absensi)
    {
        $this->authorize('checkOut', $absensi);

        $absensi->update(['check_out_at' => now()]);

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

        $absensis = $query->latest()->get();
        $filename = 'laporan-absensi-'.now()->format('Ymd_His');

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
                'absensis' => $absensis,
                'startDate' => $request->start_date,
                'endDate' => $request->end_date,
                'dapurId' => $request->dapur_id,
                'filename' => $filename,
            ]);
        }

        // Default: CSV
        $csv = "Tanggal,Pengawas,Dapur,Check-in,Check-out,Status,Lat,Lng,Foto\n";
        foreach ($absensis as $a) {
            $csv .= sprintf(
                "%s,%s,%s,%s,%s,%s,%s,%s,%s\n",
                $a->created_at->format('Y-m-d H:i:s'),
                $a->user->name,
                $a->dapur->nama,
                $a->check_in_at?->format('Y-m-d H:i:s') ?? '-',
                $a->check_out_at?->format('Y-m-d H:i:s') ?? '-',
                $a->status,
                $a->lat ?? '-',
                $a->lng ?? '-',
                $a->selfie_path ? asset('storage/'.$a->selfie_path) : '-'
            );
        }

        return response($csv)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', "attachment; filename=\"{$filename}.csv\"");
    }
}
