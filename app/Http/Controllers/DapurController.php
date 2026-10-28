<?php

namespace App\Http\Controllers;

use App\Models\Dapur;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;

class DapurController extends Controller
{
    use AuthorizesRequests;

    // Banten wilayah constants
    protected const BANTEN_PROVINSI = 'Banten';
    protected const BANTEN_KABUPATEN_KOTA = [
        'Kota Serang',
        'Kota Cilegon',
        'Kota Tangerang',
        'Kota Tangerang Selatan',
        'Kabupaten Pandeglang',
        'Kabupaten Lebak',
        'Kabupaten Tangerang',
    ];

    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Get known kecamatan for a given kabupaten/kota
     */
    protected function getKnownKecamatan(string $kabupatenKota): array
    {
        $knownKecamatan = [
            'Kota Serang' => ['Curug', 'Kasemen', 'Walantaka', 'Taktakan', 'Serang', 'Cikeusal', 'Kramatwatu', 'Pabuaran', 'Cinangka', 'Carenang'],
            'Kota Cilegon' => ['Cilegon', 'Pulo Ampel', 'Jombang', 'Citangkil', 'Grogol', 'Ciwandan', 'Gerogol'],
            'Kota Tangerang' => ['Tangerang', 'Batuceper', 'Benda', 'Jatiuwung', 'Neglasari', 'Cipondoh', 'Karawaci', 'Larangan', 'Ciledug', 'Pinang', 'Binuang', 'Pakuhaji', 'Sepatan', 'Sukadiri', 'Mekar Baru'],
            'Kota Tangerang Selatan' => ['Serpong', 'Serpong Utara', 'Pamulang', 'Ciputat', 'Ciputat Timur', 'Setu', 'Pondok Aren'],
            'Kabupaten Pandeglang' => ['Pandeglang', 'Munjul', 'Cibaliung', 'Cimanggu', 'Angsana', 'Karangsak', 'Sobang', 'Cibitung', 'Mandalawangi', 'Cikeudel', 'Panimbang', 'Sumur', 'Ujung Jaya', 'Pulosari', 'Cigeulis', 'Pagelaran', 'Majasari', 'Kaduhejo'],
            'Kabupaten Lebak' => ['Rangkasbitung', 'Cibadak', 'Muncang', 'Sobang', 'Cihara', 'Malenggang', 'Bayah', 'Cilangkap', 'Cileles', 'Cijaku', 'Cigemblong', 'Wanasalam', 'Gunung Kencana', 'Mekar Jaya', 'Panggarangan', 'Sajira', 'Cibengkung', 'Leuwidamar', 'Bojongmanik', 'Sukaresmi', 'Kencana', 'Gunung Kencana', 'Cibadak', 'Warunggunung'],
            'Kabupaten Tangerang' => ['Tigaraksa', 'Kosambi', 'Sepatan', 'Rajeg', 'Cikupa', 'Kelapa Dua', 'Curug', 'Solear', 'Kresek', 'Kaduhejo', 'Pagedangan', 'Cadasari', 'Jayanti', 'Balaraja', 'Bojongmanik', 'Kronjo', 'Mauk', 'Sukamulya', 'Gunung Kaler', 'Jasinga', 'Caringin', 'Cigemblong', 'Sindang Jaya', 'Jambe', 'Kramatwatu', 'Carenang', 'Sindang', 'Pasar Kemis'],
        ];

        return $knownKecamatan[$kabupatenKota] ?? [];
    }

    /**
     * Validate if provinsi/kabupaten/kecamatan are valid Banten data
     */
    protected function validateBantenWilayah(string $provinsi, string $kabupatenKota, ?string $kecamatan = null): array
    {
        $errors = [];

        if ($provinsi !== self::BANTEN_PROVINSI) {
            $errors[] = 'Provinsi harus Banten.';
        }

        if (!in_array($kabupatenKota, self::BANTEN_KABUPATEN_KOTA, true)) {
            $errors[] = 'Kabupaten/Kota harus di wilayah Banten.';
        }

        // Optional: validate kecamatan against known list for the kabupaten
        if ($kecamatan && $this->isKnownKecamatan($kabupatenKota, $kecamatan) === false) {
            // Warning only, not blocking - user might have more current data
            // Could log or add as warning
        }

        return $errors;
    }

    /**
     * Check if kecamatan is known for the kabupaten/kota
     */
    protected function isKnownKecamatan(string $kabupatenKota, string $kecamatan): ?bool
    {
        $knownKecamatan = $this->getKnownKecamatan($kabupatenKota);

        if (empty($knownKecamatan)) {
            return null; // Unknown kabupaten, can't validate
        }

        return in_array($kecamatan, $knownKecamatan, true);
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

        // Get unique values for filter dropdowns (admin only) - BANTEN ONLY
        $filterOptions = [];
        if ($user->isAdmin()) {
            // Force provinsi to only Banten
            $filterOptions = [
                'provinsi' => collect([self::BANTEN_PROVINSI]),
                'kabupaten_kota' => collect(self::BANTEN_KABUPATEN_KOTA)->sort()->values(),
                'kecamatan' => Dapur::whereNotNull('kecamatan')
                    ->where('provinsi', self::BANTEN_PROVINSI)
                    ->distinct()->pluck('kecamatan')->sort()->values(),
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
        ]);

        // Validate Banten wilayah
        $wilayahErrors = $this->validateBantenWilayah(
            $validated['provinsi'] ?? '',
            $validated['kabupaten_kota'] ?? '',
            $validated['kecamatan'] ?? null
        );

        if (!empty($wilayahErrors)) {
            return back()->withErrors($wilayahErrors)->withInput();
        }

        // Force provinsi to Banten
        $validated['provinsi'] = self::BANTEN_PROVINSI;

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
        ]);

        // Validate Banten wilayah
        $wilayahErrors = $this->validateBantenWilayah(
            $validated['provinsi'] ?? '',
            $validated['kabupaten_kota'] ?? '',
            $validated['kecamatan'] ?? null
        );

        if (!empty($wilayahErrors)) {
            return back()->withErrors($wilayahErrors)->withInput();
        }

        // Force provinsi to Banten
        $validated['provinsi'] = self::BANTEN_PROVINSI;

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