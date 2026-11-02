<?php

namespace App\Http\Controllers;

use App\Exports\LaporanXlsxExport;
use App\Models\Dapur;
use App\Models\Laporan;
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
        $user = auth()->user();

        // Pengawas hanya lihat laporan inspeksi sendiri
        if ($user->isPengawas()) {
            $query = Laporan::where('user_id', $user->id)->with('dapur');
        } else {
            // Admin & Manajerial lihat semua
            $query = Laporan::with('user', 'dapur');
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

        $laporans = $query->latest()->paginate(15)->withQueryString();

        // Filter dapur untuk pengawas berdasarkan dapur_user pivot
        if ($user->isPengawas()) {
            $dapurs = Dapur::forUser($user)->get();
        } else {
            $dapurs = Dapur::all();
        }

        if ($user->isPengawas()) {
            return view('pengawas.inspeksi.index', compact('laporans', 'dapurs'));
        }

        $prefix = $user->isAdmin() ? 'admin' : 'manajerial';

        return view('admin.inspeksi.index', compact('laporans', 'dapurs'))
            ->with('indexRoute', "{$prefix}.inspeksi.index")
            ->with('createRoute', "{$prefix}.inspeksi.create")
            ->with('exportRoute', "{$prefix}.inspeksi.export");
    }

    public function create()
    {
        $this->authorize('create', Laporan::class);

        $user = auth()->user();

        // Filter dapur untuk pengawas berdasarkan dapur_user pivot
        $dapurs = $user->isPengawas()
            ? Dapur::forUser($user)->get()
            : Dapur::all();

        return view('pengawas.inspeksi.index', compact('dapurs'));
    }

    public function store(Request $request)
    {
        // Store is handled by LaporanController@store
        // This method is kept for route compatibility but redirects
        return redirect()->route('pengawas.laporan.store');
    }

    public function checkOut(Request $request, Laporan $laporan)
    {
        $this->authorize('checkOut', $laporan);

        $laporan->update(['check_out_at' => now()]);

        return back()->with('status', 'Check-out berhasil dicatat.');
    }

    public function export(Request $request)
    {
        $user = auth()->user();

        $query = Laporan::with('user', 'dapur');

        if ($user->isPengawas()) {
            $query->where('user_id', $user->id);
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

        $laporans = $query->latest()->get();
        $filename = 'laporan-inspeksi-'.now()->format('Ymd_His');

        if ($request->get('format') === 'xlsx') {
            $exporter = new LaporanXlsxExport(
                $request->start_date,
                $request->end_date,
                $request->dapur_id
            );

            return response($exporter->generate())
                ->header('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet')
                ->header('Content-Disposition', "attachment; filename=\"{$filename}.xlsx\"");
        }

        // Default: CSV
        $csv = "Tanggal,Pengawas,Dapur,Status,Catatan,Latitude,Longitude\n";
        foreach ($laporans as $l) {
            $csv .= sprintf(
                "%s,%s,%s,%s,%s,%s,%s\n",
                $l->created_at->format('Y-m-d H:i:s'),
                $l->user->name ?? '-',
                $l->dapur->nama ?? '-',
                $l->status,
                $l->catatan ?? '-',
                $l->latitude ?? '-',
                $l->longitude ?? '-'
            );
        }

        return response($csv)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', "attachment; filename=\"{$filename}.csv\"");
    }
}