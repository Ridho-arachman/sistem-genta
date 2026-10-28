<?php

namespace App\Http\Controllers;

use App\Models\Dapur;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;

class DapurController extends Controller
{
    use AuthorizesRequests;

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $user = auth()->user();

        if (! $user->hasRole(['admin', 'manajerial', 'pengawas', 'kepala_dapur'])) {
            abort(403);
        }

        $query = Dapur::forUser($user)
            ->with(['pengawas', 'kepalaDapur'])
            ->withCount('cctvs');

        // Filter by wilayah (admin only)
        if ($user->isAdmin() && request()->has(['provinsi', 'kabupaten_kota', 'kecamatan'])) {
            $query->when(request('provinsi'), fn ($q, $v) => $q->where('provinsi', $v));
            $query->when(request('kabupaten_kota'), fn ($q, $v) => $q->where('kabupaten_kota', $v));
            $query->when(request('kecamatan'), fn ($q, $v) => $q->where('kecamatan', $v));
        }

        $dapurs = $query->latest()->paginate(10);

        // Prepare dapur data for Alpine.js component (admin view only)
        $dapurData = [];
        if ($user->isAdmin()) {
            $dapurData = $dapurs->getCollection()->map(function ($d) {
                $pengawas = $d->pengawas->map(fn ($p) => ['id' => $p->id, 'name' => $p->name])->toArray();
                $kepala = $d->kepalaDapur ? ['id' => $d->kepalaDapur->id, 'name' => $d->kepalaDapur->name] : null;
                $cctvCount = $d->cctvs_count ?? 0;
                $maxCams = 4;
                $progress = $maxCams > 0 ? min(100, round(($cctvCount / $maxCams) * 100)) : 0;

                $status = 'Operasional';
                $statusClass = 'ok';
                if ($cctvCount === 0) {
                    $status = 'Kamera bermasalah';
                    $statusClass = 'off';
                } elseif ($cctvCount < $maxCams) {
                    $status = 'Perlu perhatian';
                    $statusClass = 'warn';
                }

                // Build lokasiPengawas string
                $lokasiPengawas = $d->lokasi ?? ($d->kabupaten_kota ? "{$d->kabupaten_kota} · {$d->kecamatan}" : '—');
                if ($d->pengawas->count() > 0) {
                    $lokasiPengawas .= ' · Pengawas: ' . $d->pengawas->first()->name;
                }

                return [
                    'id' => $d->id,
                    'nama' => $d->nama,
                    'lokasi' => $d->lokasi,
                    'kecamatan' => $d->kecamatan,
                    'kabupaten_kota' => $d->kabupaten_kota,
                    'provinsi' => $d->provinsi,
                    'lokasiPengawas' => $lokasiPengawas,
                    'alamat' => $d->alamat,
                    'pengawas' => $pengawas,
                    'kepalaDapur' => $kepala,
                    'cctvs_count' => $cctvCount,
                    'maxCams' => $maxCams,
                    'progress' => $progress,
                    'status' => $status,
                    'statusClass' => $statusClass,
                ];
            })->toArray();
        }

        // Get unique values for filter dropdowns (admin only)
        $filterOptions = [];
        if ($user->isAdmin()) {
            $filterOptions = [
                'provinsi' => Dapur::whereNotNull('provinsi')->distinct()->pluck('provinsi')->sort()->values(),
                'kabupaten_kota' => Dapur::whereNotNull('kabupaten_kota')->distinct()->pluck('kabupaten_kota')->sort()->values(),
                'kecamatan' => Dapur::whereNotNull('kecamatan')->distinct()->pluck('kecamatan')->sort()->values(),
            ];
        }

        if ($user->isManajerial()) {
            return view('manajerial.dapurs.index', compact('dapurs'));
        }

        if ($user->isPengawas()) {
            return view('pengawas.dapurs.index', compact('dapurs'));
        }

        if ($user->isKepalaDapur()) {
            return view('kepala-dapur.dapurs.index', compact('dapurs'));
        }

        return view('admin.dapurs.index', compact('dapurs', 'dapurData', 'filterOptions'));
    }

    public function create()
    {
        if (! auth()->user()->isAdmin()) {
            abort(403, 'Akses hanya untuk admin.');
        }

        return view('admin.dapurs.create');
    }

    public function store(Request $request)
    {
        if (! auth()->user()->isAdmin()) {
            abort(403);
        }

        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:255'],
            'lokasi' => ['nullable', 'string', 'max:255'],
            'alamat' => ['nullable', 'string'],
            'kecamatan' => ['nullable', 'string', 'max:100'],
            'kabupaten_kota' => ['nullable', 'string', 'max:100'],
            'provinsi' => ['nullable', 'string', 'max:100'],
            'latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180'],
            'geofence_radius' => ['nullable', 'integer', 'min:10', 'max:5000'],
        ]);

        Dapur::create($validated);

        return redirect()->route('admin.dapurs.index')->with('status', 'Dapur berhasil ditambahkan.');
    }

    public function show(Dapur $dapur)
    {
        $user = auth()->user();

        if (! $user->hasRole(['admin', 'manajerial', 'pengawas', 'kepala_dapur'])) {
            abort(403);
        }

        // Check if user can access this specific dapur
        if (! $user->canAccessDapur($dapur->id)) {
            abort(403);
        }

        $dapur->load(['cctvs', 'pengawas', 'kepalaDapur']);

        if ($user->isManajerial()) {
            return view('manajerial.dapurs.show', compact('dapur'));
        }

        if ($user->isPengawas()) {
            return view('pengawas.dapurs.show', compact('dapur'));
        }

        if ($user->isKepalaDapur()) {
            return view('kepala-dapur.dapurs.show', compact('dapur'));
        }

        return view('admin.dapurs.show', compact('dapur'));
    }

    public function edit(Dapur $dapur)
    {
        if (! auth()->user()->isAdmin()) {
            abort(403);
        }

        return view('admin.dapurs.edit', compact('dapur'));
    }

    public function update(Request $request, Dapur $dapur)
    {
        if (! auth()->user()->isAdmin()) {
            abort(403);
        }

        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:255'],
            'lokasi' => ['nullable', 'string', 'max:255'],
            'alamat' => ['nullable', 'string'],
            'kecamatan' => ['nullable', 'string', 'max:100'],
            'kabupaten_kota' => ['nullable', 'string', 'max:100'],
            'provinsi' => ['nullable', 'string', 'max:100'],
            'latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180'],
            'geofence_radius' => ['nullable', 'integer', 'min:10', 'max:5000'],
        ]);

        $dapur->update($validated);

        return redirect()->route('admin.dapurs.index')->with('status', 'Dapur berhasil diperbarui.');
    }

    public function destroy(Dapur $dapur)
    {
        if (! auth()->user()->isAdmin()) {
            abort(403);
        }

        $dapur->delete();

        return redirect()->route('admin.dapurs.index')->with('status', 'Dapur berhasil dihapus.');
    }
}