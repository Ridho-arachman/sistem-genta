<?php

namespace App\Http\Controllers;

use App\Jobs\CctvAlertJob;
use App\Models\Cctv;
use App\Models\Dapur;
use App\Services\MediaMtxClient;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;

class CctvController extends Controller
{
    use AuthorizesRequests;

    public function __construct(protected MediaMtxClient $mediaMtx)
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $user = auth()->user();

        if (! $user->hasRole(['admin', 'manajerial', 'kepala_dapur', 'pengawas'])) {
            abort(403);
        }

        // Get filter values from query string
        $provinsi = $request->query('provinsi', 'Banten');
        $kabupatenKota = $request->query('kabupaten_kota');
        $kecamatan = $request->query('kecamatan');
        $dapurId = $request->query('dapur_id');

        // Get accessible dapur IDs for the user
        $dapurIds = $user->getAccessibleDapurIds();

        // Build dapur query with role-based access
        $dapurQuery = Dapur::whereIn('id', $dapurIds)
            ->where('provinsi', $provinsi);

        // Get distinct options for dropdowns
        // Kabupaten: show ALL kabupaten_kota in provinsi (don't filter by kabupaten_kota)
        $kabupatenKotas = Dapur::whereIn('id', $dapurIds)
            ->where('provinsi', $provinsi)
            ->distinct()
            ->pluck('kabupaten_kota', 'kabupaten_kota')
            ->sort();

        // Kecamatan: filter by kabupaten_kota only if kabupaten_kota is selected
        $kecamatanQuery = Dapur::whereIn('id', $dapurIds)
            ->where('provinsi', $provinsi);
        if ($kabupatenKota) {
            $kecamatanQuery->where('kabupaten_kota', $kabupatenKota);
        }
        $kecamatans = $kecamatanQuery->distinct()->pluck('kecamatan', 'kecamatan')->sort();

        // Get filtered dapurs for actual CCTV results
        $filteredDapurs = Dapur::whereIn('id', $dapurIds)
            ->where('provinsi', $provinsi);
        if ($kabupatenKota) {
            $filteredDapurs = $filteredDapurs->where('kabupaten_kota', $kabupatenKota);
        }
        if ($kecamatan) {
            $filteredDapurs = $filteredDapurs->where('kecamatan', $kecamatan);
        }
        $filteredDapurs = $filteredDapurs->get();

        // Build CCTV query
        $cctvQuery = Cctv::with('dapur')->whereIn('dapur_id', $filteredDapurs->pluck('id'));

        // If specific dapur selected, filter by it
        if ($dapurId) {
            $cctvQuery->where('dapur_id', $dapurId);
        }

        $cctvs = $cctvQuery->orderBy('dapur_id')->orderBy('camera_number')->paginate(10);

        if ($user->isKepalaDapur()) {
            return view('kepala-dapur.cctvs.index', compact('cctvs', 'kabupatenKotas', 'kecamatans', 'filteredDapurs', 'provinsi', 'kabupatenKota', 'kecamatan', 'dapurId'));
        } elseif ($user->isManajerial()) {
            return view('manajerial.cctvs.index', compact('cctvs', 'kabupatenKotas', 'kecamatans', 'filteredDapurs', 'provinsi', 'kabupatenKota', 'kecamatan', 'dapurId'));
        } elseif ($user->isPengawas()) {
            return view('pengawas.cctvs.index', compact('cctvs', 'kabupatenKotas', 'kecamatans', 'filteredDapurs', 'provinsi', 'kabupatenKota', 'kecamatan', 'dapurId'));
        }

        return view('admin.cctvs.index', compact('cctvs', 'kabupatenKotas', 'kecamatans', 'filteredDapurs', 'provinsi', 'kabupatenKota', 'kecamatan', 'dapurId'));
    }

    public function create()
    {
        if (! auth()->user()->isAdmin()) {
            abort(403, 'Hanya admin yang bisa menambah CCTV.');
        }

        $dapurs = Dapur::all();

        return view('admin.cctvs.create', compact('dapurs'));
    }

    public function store(Request $request)
    {
        if (! auth()->user()->isAdmin()) {
            abort(403);
        }

        $validated = $request->validate([
            'dapur_id' => ['required', 'exists:dapurs,id'],
            'nama' => ['required', 'string', 'max:255'],
            'rtsp_url' => ['nullable', 'string', 'max:500', 'starts_with:rtsp://,rtsps://'],
            'status' => ['required', 'in:online,offline,maintenance'],
            'section' => ['nullable', 'string', 'max:255'],
            'camera_number' => ['nullable', 'integer', 'min:1'],
        ]);

        $cctv = Cctv::create($validated);
        $this->syncStreamPath($cctv);

        return redirect()->route('admin.cctvs.index')->with('status', 'CCTV berhasil ditambahkan.');
    }

    public function show(Cctv $cctv)
    {
        $user = auth()->user();

        if (! $user->hasRole(['admin', 'manajerial', 'kepala_dapur', 'pengawas'])) {
            abort(403);
        }

        if (! $user->canAccessDapur($cctv->dapur_id)) {
            abort(403);
        }

        $cctv->load('dapur');

        if ($user->isKepalaDapur()) {
            return view('kepala-dapur.cctvs.show', compact('cctv'));
        } elseif ($user->isManajerial()) {
            return view('manajerial.cctvs.show', compact('cctv'));
        } elseif ($user->isPengawas()) {
            return view('pengawas.cctvs.show', compact('cctv'));
        }

        return view('admin.cctvs.show', compact('cctv'));
    }

    public function edit(Cctv $cctv)
    {
        if (! auth()->user()->isAdmin()) {
            abort(403);
        }

        $dapurs = Dapur::all();

        return view('admin.cctvs.edit', compact('cctv', 'dapurs'));
    }

    public function update(Request $request, Cctv $cctv)
    {
        if (! auth()->user()->isAdmin()) {
            abort(403);
        }

        $validated = $request->validate([
            'dapur_id' => ['required', 'exists:dapurs,id'],
            'nama' => ['required', 'string', 'max:255'],
            'rtsp_url' => ['nullable', 'string', 'max:500', 'starts_with:rtsp://,rtsps://'],
            'status' => ['required', 'in:online,offline,maintenance'],
            'section' => ['nullable', 'string', 'max:255'],
            'camera_number' => ['nullable', 'integer', 'min:1'],
        ]);

        $oldStatus = $cctv->status;
        $cctv->update($validated);
        $this->syncStreamPath($cctv);

        if ($oldStatus !== $cctv->status && in_array($cctv->status, ['offline', 'maintenance'])) {
            CctvAlertJob::dispatch($cctv->id, $cctv->status, true);
        }

        return redirect()->route('admin.cctvs.index')->with('status', 'CCTV berhasil diperbarui.');
    }

    public function destroy(Cctv $cctv)
    {
        if (! auth()->user()->isAdmin()) {
            abort(403);
        }

        if ($cctv->stream_key) {
            $this->mediaMtx->removePath($cctv->stream_key);
        }

        $cctv->delete();

        return redirect()->route('admin.cctvs.index')->with('status', 'CCTV berhasil dihapus.');
    }

    /**
     * Sinkronkan path MediaMTX untuk kamera ini (mode pull kalau ada rtsp_url).
     */
    private function syncStreamPath(Cctv $cctv): void
    {
        if ($cctv->stream_key) {
            $this->mediaMtx->addPath($cctv->stream_key, $cctv->rtsp_url);
        }
    }

    public function monitoringDashboard()
    {
        $user = auth()->user();

        if (! $user->hasRole(['admin', 'manajerial', 'kepala_dapur', 'pengawas'])) {
            abort(403);
        }

        $dapurIds = $user->getAccessibleDapurIds();

        // Batasi jumlah tile per halaman supaya tidak membuka ribuan stream sekaligus.
        $cctvs = Cctv::with('dapur')
            ->whereIn('dapur_id', $dapurIds)
            ->orderBy('dapur_id')
            ->orderBy('camera_number')
            ->paginate(24);

        if ($user->isKepalaDapur()) {
            return view('kepala-dapur.cctvs.monitoring-dashboard', compact('cctvs'));
        } elseif ($user->isManajerial()) {
            return view('manajerial.cctvs.monitoring-dashboard', compact('cctvs'));
        } elseif ($user->isPengawas()) {
            return view('pengawas.cctvs.monitoring-dashboard', compact('cctvs'));
        }

        return view('admin.cctvs.monitoring-dashboard', compact('cctvs'));
    }
}
