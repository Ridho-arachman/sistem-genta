<?php

namespace App\Http\Controllers;

use App\Jobs\CctvAlertJob;
use App\Jobs\CctvHealthCheckJob;
use App\Models\Cctv;
use App\Models\Dapur;
use App\Services\MediaMtxClient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CctvController extends Controller
{
    public function __construct(protected MediaMtxClient $mediaMtx) {}

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

        // Manajerial & kepala dapur memakai tampilan yang sama; tombol tulis
        // disembunyikan lewat @if di view (mereka tidak punya route tulis).
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
        // Sumber butuh waktu mulai; cek khusus kamera ini setelah path siap.
        CctvHealthCheckJob::dispatch(
            $cctv->dapur_id,
            $cctv->id,
            Log::sharedContext()['correlation_id'] ?? null
        )->delay(now()->addSeconds(5));

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

        $cctv->load(['dapur', 'anomalyReports.admin']);

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
        // Path baru butuh waktu mulai; cek langsung sering salah tandai offline.
        CctvHealthCheckJob::dispatch(
            $cctv->dapur_id,
            $cctv->id,
            Log::sharedContext()['correlation_id'] ?? null
        )->delay(now()->addSeconds(5));

        if ($oldStatus !== $cctv->status && in_array($cctv->status, ['offline', 'maintenance'])) {
            CctvAlertJob::dispatch(
                $cctv->id,
                $cctv->status,
                true,
                Log::sharedContext()['correlation_id'] ?? null
            );
        }

        return redirect()->route('admin.cctvs.index')->with('status', 'CCTV berhasil diperbarui.');
    }

    public function destroy(Cctv $cctv)
    {
        if (! auth()->user()->isAdmin()) {
            abort(403);
        }

        // Soft delete: jangan hapus path MediaMTX, biar bisa dipulihkan.
        $cctv->delete();

        return redirect()->route('admin.cctvs.index')->with('status', 'CCTV berhasil dihapus (arsip).');
    }

    public function archive(Request $request)
    {
        if (! auth()->user()->isAdmin()) {
            abort(403);
        }

        $query = Cctv::onlyTrashed()->with('dapur');

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('nama', 'like', "%{$request->search}%")
                    ->orWhere('rtsp_url', 'like', "%{$request->search}%");
            });
        }

        $cctvs = $query->latest()->paginate(15)->withQueryString();

        return view('admin.cctvs.archive', compact('cctvs'));
    }

    public function restore(Request $request, $id)
    {
        if (! auth()->user()->isAdmin()) {
            abort(403);
        }

        $cctv = Cctv::withTrashed()->findOrFail($id);
        $cctv->restore();

        return redirect()->route('admin.cctvs.archive')->with('status', 'CCTV berhasil dipulihkan.');
    }

    public function forceDelete(Request $request, $id)
    {
        if (! auth()->user()->isAdmin()) {
            abort(403);
        }

        $cctv = Cctv::withTrashed()->findOrFail($id);

        if ($cctv->stream_key) {
            $this->mediaMtx->removePath($cctv->stream_key);
        }

        $cctv->forceDelete();

        return redirect()->route('admin.cctvs.archive')->with('status', 'CCTV berhasil dihapus permanen.');
    }

    /**
     * Sinkronkan path MediaMTX untuk kamera ini (mode pull kalau ada rtsp_url).
     */
    private function syncStreamPath(Cctv $cctv): void
    {
        if (! $cctv->stream_key) {
            return;
        }

        $this->mediaMtx->addPath($cctv->stream_key, $cctv->rtsp_url);
    }
}
