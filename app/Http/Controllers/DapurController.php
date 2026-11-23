<?php

namespace App\Http\Controllers;

use App\Models\Dapur;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;

class DapurController extends Controller
{
    use AuthorizesRequests;

    public function index()
    {
        $user = auth()->user();

        if (! $user->hasRole(['admin', 'manajerial', 'pengawas', 'kepala_dapur'])) {
            abort(403);
        }

        $query = Dapur::forUser($user)
            ->with(['pengawas', 'kepalaDapur'])
            ->withCount('cctvs')
            ->withCount(['cctvs as cctvs_online_count' => fn ($q) => $q->where('status', 'online')]);

        // Filter by wilayah (admin only)
        if ($user->isAdmin() && request()->has(['provinsi', 'kabupaten_kota', 'kecamatan'])) {
            $query->when(request('provinsi'), fn ($q, $v) => $q->where('provinsi', $v));
            $query->when(request('kabupaten_kota'), fn ($q, $v) => $q->where('kabupaten_kota', $v));
            $query->when(request('kecamatan'), fn ($q, $v) => $q->where('kecamatan', $v));
        }

        $dapurs = $query->latest()->paginate(10);

        // Prepare dapur data for Alpine.js component (dipakai admin & role read-only yang share view ini)
        $dapurData = $dapurs->getCollection()->map(function ($d) {
            $pengawas = $d->pengawas->map(fn ($p) => ['id' => $p->id, 'name' => $p->name])->toArray();
            $kepala = $d->kepalaDapur ? ['id' => $d->kepalaDapur->id, 'name' => $d->kepalaDapur->name] : null;
            $totalCctvs = $d->cctvs_count ?? 0;
            $onlineCctvs = $d->cctvs_online_count ?? 0;
            $progress = $totalCctvs > 0 ? min(100, round(($onlineCctvs / $totalCctvs) * 100)) : 0;

            $status = 'Operasional';
            $statusClass = 'ok';
            if ($totalCctvs === 0) {
                $status = 'Kamera bermasalah';
                $statusClass = 'off';
            } elseif ($onlineCctvs === 0) {
                $status = 'Kamera bermasalah';
                $statusClass = 'off';
            } elseif ($onlineCctvs < $totalCctvs) {
                $status = 'Perlu perhatian';
                $statusClass = 'warn';
            }

            // Build lokasiPengawas string
            $lokasiPengawas = $d->kabupaten_kota ? "{$d->kabupaten_kota} · Kec. {$d->kecamatan}" : '—';
            if ($d->pengawas->count() > 0) {
                $lokasiPengawas .= ' · Pengawas: '.$d->pengawas->first()->name;
            }

            return [
                'id' => $d->id,
                'nama' => $d->nama,
                'kecamatan' => $d->kecamatan,
                'kabupaten_kota' => $d->kabupaten_kota,
                'provinsi' => $d->provinsi,
                'lokasiPengawas' => $lokasiPengawas,
                'alamat' => $d->alamat,
                'pengawas' => $pengawas,
                'kepalaDapur' => $kepala,
                'cctvs_count_total' => $totalCctvs,
                'cctvs_count_online' => $onlineCctvs,
                'progress' => $progress,
                'status' => $status,
                'statusClass' => $statusClass,
            ];
        })->toArray();

        // Dropdown kabupaten/kota/kecamatan dari satu sumber: config('banten.*')
        $allKecamatanMap = collect(config('banten.kabupaten_kota'))->mapWithKeys(fn ($kab) => [
            $kab => collect(config('banten.kecamatan')[$kab] ?? [])->values(),
        ]);

        $filterOptions = [];
        if ($user->isAdmin()) {
            $filterOptions = [
                'provinsi' => collect([config('banten.provinsi')]),
                'kabupaten_kota' => collect(config('banten.kabupaten_kota'))->sort()->values(),
                'kecamatan' => $allKecamatanMap->flatten()->unique()->sort()->values(),
            ];
        }

        // Manajerial & kepala dapur memakai tampilan admin (read-only via guard tombol di view).
        return view('admin.dapurs.index', compact('dapurs', 'dapurData', 'filterOptions', 'allKecamatanMap'));
    }

    public function create()
    {
        if (! auth()->user()->isAdmin()) {
            abort(403, 'Akses hanya untuk admin.');
        }

        $kecamatanMapping = config('banten.kecamatan');

        return view('admin.dapurs.create', compact('kecamatanMapping'));
    }

    public function store(Request $request)
    {
        if (! auth()->user()->isAdmin()) {
            abort(403);
        }

        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:255'],
            'alamat' => ['nullable', 'string'],
            'map_embed_url' => ['nullable', 'url', 'max:2000'],
            'kecamatan' => ['required', 'string', 'max:100'],
            'kabupaten_kota' => ['required', 'string', 'max:100'],
        ]);

        // Kabupaten/kota harus ada di daftar wilayah Banten (sumber: config('banten.*'))
        if (! in_array($validated['kabupaten_kota'], config('banten.kabupaten_kota'), true)) {
            return back()->withErrors(['kabupaten_kota' => 'Kabupaten/Kota harus di wilayah Banten.'])->withInput();
        }

        $validated['provinsi'] = config('banten.provinsi');

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

        if ($user->isPengawas()) {
            return view('pengawas.dapurs.show', compact('dapur'));
        }

        // Manajerial & kepala dapur: tampilan admin read-only (tombol tulis disembunyikan di view).
        return view('admin.dapurs.show', compact('dapur'));
    }

    public function edit(Dapur $dapur)
    {
        if (! auth()->user()->isAdmin()) {
            abort(403);
        }

        $kecamatanMapping = config('banten.kecamatan');

        return view('admin.dapurs.edit', compact('dapur', 'kecamatanMapping'));
    }

    public function update(Request $request, Dapur $dapur)
    {
        if (! auth()->user()->isAdmin()) {
            abort(403);
        }

        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:255'],
            'alamat' => ['nullable', 'string'],
            'map_embed_url' => ['nullable', 'url', 'max:2000'],
            'kecamatan' => ['required', 'string', 'max:100'],
            'kabupaten_kota' => ['required', 'string', 'max:100'],
        ]);

        // Kabupaten/kota harus ada di daftar wilayah Banten (sumber: config('banten.*'))
        if (! in_array($validated['kabupaten_kota'], config('banten.kabupaten_kota'), true)) {
            return back()->withErrors(['kabupaten_kota' => 'Kabupaten/Kota harus di wilayah Banten.'])->withInput();
        }

        $validated['provinsi'] = config('banten.provinsi');

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
