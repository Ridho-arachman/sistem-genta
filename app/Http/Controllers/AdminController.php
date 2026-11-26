<?php

namespace App\Http\Controllers;

use App\Models\Cctv;
use App\Models\CctvAnomalyReport;
use App\Models\Dapur;
use App\Models\Laporan;
use App\Models\Settings;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class AdminController extends Controller
{
    // User Management
    public function users(Request $request)
    {
        $query = User::with(['registeredBy', 'dapur', 'dapurs'])->latest();

        $query->active();

        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', "%{$request->search}%")
                    ->orWhere('email', 'like', "%{$request->search}%");
            });
        }

        $users = $query->paginate(15)->withQueryString();

        return view('admin.users.index', compact('users'));
    }

    public function createUser()
    {
        $dapurs = Dapur::orderBy('nama')->with('kepalaDapur')->get();
        $dapurData = $dapurs->map(fn ($d) => [
            'id' => $d->id,
            'nama' => $d->nama,
            'wilayah' => $d->wilayah,
            'kepala_dapur_name' => $d->kepalaDapur ? $d->kepalaDapur->name : null,
        ])->toArray();

        return view('admin.users.create', compact('dapurs', 'dapurData'));
    }

    public function storeUser(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Password::defaults()],
            'role' => ['required', 'in:pengawas,manajerial,kepala_dapur'],
            'dapur_id' => ['nullable', 'exists:dapurs,id'],
            'dapur_ids' => ['nullable', 'array'],
            'dapur_ids.*' => ['exists:dapurs,id'],
        ]); // Pesan khusus dapur_id/dapur_ids ada di blok role-specific di bawah (di sini nullable, jadi tak pernah memicu).

        // Role-specific validation
        if ($validated['role'] === 'kepala_dapur') {
            $request->validate([
                'dapur_id' => ['required', 'exists:dapurs,id'],
            ], [
                'dapur_id.required' => 'Dapur wajib dipilih untuk Kepala Dapur.',
            ]);

            // Check if dapur already has a kepala_dapur
            $existingKepala = User::where('role', 'kepala_dapur')
                ->where('dapur_id', $validated['dapur_id'])
                ->first();
            if ($existingKepala) {
                return back()->withErrors([
                    'dapur_id' => 'Dapur ini sudah memiliki Kepala Dapur: '.$existingKepala->name.'. Menimpa akan mengubah assignment.',
                ])->withInput();
            }
        }

        if ($validated['role'] === 'pengawas') {
            $request->validate([
                'dapur_ids' => ['required', 'array', 'min:1'],
                'dapur_ids.*' => ['exists:dapurs,id'],
            ], [
                'dapur_ids.required' => 'Minimal 1 dapur wajib dipilih untuk Pengawas.',
                'dapur_ids.min' => 'Minimal 1 dapur wajib dipilih untuk Pengawas.',
            ]);
        }

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'],
            'registered_by' => auth()->id(),
            'dapur_id' => $validated['dapur_id'] ?? null,
        ]);

        // Sync multi-dapur for pengawas
        if ($user->isPengawas() && ! empty($validated['dapur_ids'])) {
            $user->dapurs()->sync($validated['dapur_ids']);
        }

        return redirect()->route('admin.users')->with('status', 'User berhasil dibuat.');
    }

    public function editUser(User $user)
    {
        if ($user->id === auth()->id()) {
            abort(403, 'Tidak bisa mengedit akun sendiri.');
        }
        if ($user->isAdmin()) {
            abort(403, 'Tidak bisa mengedit admin lain.');
        }

        $dapurs = Dapur::orderBy('nama')->with('kepalaDapur')->get();
        $dapurData = $dapurs->map(fn ($d) => [
            'id' => $d->id,
            'nama' => $d->nama,
            'wilayah' => $d->wilayah,
            'kepala_dapur_name' => $d->kepalaDapur ? $d->kepalaDapur->name : null,
        ])->toArray();

        return view('admin.users.edit', compact('user', 'dapurs', 'dapurData'));
    }

    public function updateUser(Request $request, User $user)
    {
        if ($user->id === auth()->id()) {
            abort(403);
        }
        if ($user->isAdmin()) {
            abort(403);
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,'.$user->id],
            'role' => ['required', 'in:pengawas,manajerial,kepala_dapur'],
            'dapur_id' => ['nullable', 'exists:dapurs,id'],
            'dapur_ids' => ['nullable', 'array'],
            'dapur_ids.*' => ['exists:dapurs,id'],
            'password' => ['nullable', 'confirmed', Password::defaults()],
        ], [
            'dapur_id.required' => 'Dapur wajib dipilih untuk Kepala Dapur.',
            'dapur_ids.required' => 'Minimal 1 dapur wajib dipilih untuk Pengawas.',
            'dapur_ids.min' => 'Minimal 1 dapur wajib dipilih untuk Pengawas.',
        ]);

        // Role-specific validation
        if ($validated['role'] === 'kepala_dapur') {
            $request->validate([
                'dapur_id' => ['required', 'exists:dapurs,id'],
            ], [
                'dapur_id.required' => 'Dapur wajib dipilih untuk Kepala Dapur.',
            ]);

            // Check if dapur already has a kepala_dapur (other than this user)
            $existingKepala = User::where('role', 'kepala_dapur')
                ->where('dapur_id', $validated['dapur_id'])
                ->where('id', '!=', $user->id)
                ->first();
            if ($existingKepala) {
                return back()->withErrors([
                    'dapur_id' => 'Dapur ini sudah memiliki Kepala Dapur: '.$existingKepala->name.'. Menimpa akan mengubah assignment.',
                ])->withInput();
            }
        }

        if ($validated['role'] === 'pengawas') {
            $request->validate([
                'dapur_ids' => ['required', 'array', 'min:1'],
                'dapur_ids.*' => ['exists:dapurs,id'],
            ], [
                'dapur_ids.required' => 'Minimal 1 dapur wajib dipilih untuk Pengawas.',
                'dapur_ids.min' => 'Minimal 1 dapur wajib dipilih untuk Pengawas.',
            ]);
        }

        $update = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'role' => $validated['role'],
            'dapur_id' => $validated['dapur_id'] ?? null,
        ];

        // Password baru dari form edit user.
        if ($validated['password'] ?? null) {
            $update['password'] = Hash::make($validated['password']);
        }

        $user->update($update);

        // Sync multi-dapur for pengawas
        if ($user->isPengawas()) {
            $user->dapurs()->sync($validated['dapur_ids'] ?? []);
        } elseif ($user->isKepalaDapur()) {
            // Kepala dapur hanya punya 1 dapur (dapur_id)
            $user->dapurs()->detach();
        }

        return redirect()->route('admin.users')->with('status', 'User berhasil diperbarui.');
    }

    public function destroyUser(User $user)
    {
        if ($user->id === auth()->id()) {
            abort(403, 'Tidak bisa menghapus akun sendiri.');
        }
        if ($user->isAdmin()) {
            abort(403, 'Tidak bisa menghapus admin.');
        }

        $user->delete();

        return redirect()->route('admin.users')->with('status', 'User berhasil dihapus (soft delete).');
    }

    // Archived Users (Soft Deleted)
    public function archivedUsers(Request $request)
    {
        $query = User::onlyTrashed()->with(['registeredBy', 'dapur', 'dapurs'])->latest();

        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', "%{$request->search}%")
                    ->orWhere('email', 'like', "%{$request->search}%");
            });
        }

        $users = $query->paginate(15)->withQueryString();

        return view('admin.users.archive', compact('users'));
    }

    public function restoreUser($id)
    {
        $user = User::withTrashed()->findOrFail($id);

        if ($user->isAdmin()) {
            abort(403, 'Tidak bisa restore admin.');
        }

        $user->restore();

        return redirect()->route('admin.users.archive')->with('status', 'User berhasil dipulihkan.');
    }

    public function forceDeleteUser($id)
    {
        $user = User::withTrashed()->findOrFail($id);

        if ($user->id === auth()->id()) {
            abort(403, 'Tidak bisa menghapus akun sendiri.');
        }
        if ($user->isAdmin()) {
            abort(403, 'Tidak bisa menghapus admin.');
        }

        $user->forceDelete();

        return redirect()->route('admin.users.archive')->with('status', 'User berhasil dihapus permanen.');
    }

    // Dashboard Stats
    public function dashboard()
    {
        $totalCctv = Cctv::count();
        $cctvOnline = Cctv::where('status', 'online')->count();
        $laporanHariIni = Laporan::whereDate('created_at', today())->count();
        $laporanHariIniSelesai = Laporan::whereDate('created_at', today())->where('status', 'selesai')->count();

        $stats = [
            'total_dapur' => Dapur::count(),
            'total_cctv' => $totalCctv,
            'cctv_online' => $cctvOnline,
            'anomaly_reports_open' => CctvAnomalyReport::where('status', '!=', 'selesai')->count(),
            'laporan_hari_ini' => $laporanHariIni,
            'laporan_hari_ini_selesai' => $laporanHariIniSelesai,
            'total_pengawas' => User::where('role', 'pengawas')->active()->count(),
            'total_manajerial' => User::where('role', 'manajerial')->active()->count(),
            'total_kepala_dapur' => User::where('role', 'kepala_dapur')->active()->count(),
            'pengawas_without_dapur' => User::where('role', 'pengawas')
                ->active()
                ->whereDoesntHave('dapurs')
                ->count(),
            'kepala_dapur_without_dapur' => User::where('role', 'kepala_dapur')
                ->whereNull('dapur_id')
                ->count(),
        ];

        $dapurs = Dapur::withCount([
            'cctvs',
            'cctvs as cctvs_online_count' => fn ($q) => $q->where('status', 'online'),
        ])
            ->with(['users' => function ($q) {
                $q->where('role', 'kepala_dapur');
            }])
            ->get();

        // Pengawas assignment overview
        $pengawas = User::where('role', 'pengawas')
            ->with('dapurs')
            ->get()
            ->map(function ($p) {
                return [
                    'id' => $p->id,
                    'name' => $p->name,
                    'email' => $p->email,
                    'dapur_count' => $p->dapurs->count(),
                    'dapur_names' => $p->dapurs->pluck('nama')->implode(', '),
                ];
            });

        $recentLaporans = Laporan::with('createdBy', 'dapur')
            ->latest()
            ->limit(10)
            ->get();

        $recentAnomalies = CctvAnomalyReport::with('cctv.dapur', 'admin')
            ->latest()
            ->limit(10)
            ->get();

        // Kandidat preview CCTV: prioritas yang punya stream key (stream_url/web_rtc_url
        // adalah accessor turunan dari stream_key, bukan kolom DB).
        $previewCctvs = Cctv::with('dapur')
            ->whereNotNull('stream_key')
            ->orderByRaw("CASE status WHEN 'online' THEN 1 WHEN 'maintenance' THEN 2 ELSE 3 END")
            ->latest('last_checked_at')
            ->limit(10)
            ->get()
            ->map(fn ($c) => [
                'id' => $c->id,
                'nama' => $c->nama ?? 'Kamera',
                'dapur' => $c->dapur?->nama ?? '-',
                'status' => $c->status,
                'stream_url' => $c->stream_url,
                'web_rtc_url' => $c->web_rtc_url,
                'show_url' => route('admin.cctvs.show', $c),
            ]);

        if ($previewCctvs->isEmpty()) {
            $previewCctvs = Cctv::with('dapur')->latest('last_checked_at')->limit(10)->get()->map(fn ($c) => [
                'id' => $c->id,
                'nama' => $c->nama ?? 'Kamera',
                'dapur' => $c->dapur?->nama ?? '-',
                'status' => $c->status,
                'stream_url' => $c->stream_url,
                'web_rtc_url' => $c->web_rtc_url,
                'show_url' => route('admin.cctvs.show', $c),
            ]);
        }

        // Aktivitas terbaru gabungan dari laporan dan anomali, diurutkan terbaru.
        // 4 tipe real (tanpa palsukan riwayat status kamera yang tidak ada di DB):
        //  1. Laporan inspeksi dikirim     ✓ hijau  (Laporan baru, status diperiksa)
        //  2. Temuan perlu tindak lanjut   ⚑ kuning (Laporan status temuan/tindak lanjut)
        //  3. Laporan inspeksi diperiksa   ▤ ungu   (Laporan status selesai)
        //  4. Temuan anomali CCTV          ▣ biru   (CctvAnomalyReport baru)
        $activities = collect();
        foreach ($recentLaporans as $laporan) {
            $status = $laporan->status;
            if ($status === 'selesai') {
                $activities->push([
                    'type' => 'laporan_diperiksa',
                    'title' => 'Laporan inspeksi diperiksa',
                    'subtitle' => ($laporan->createdBy?->name ?? 'Admin').' · '.($laporan->dapur?->nama ?? 'Dapur'),
                    'time' => $laporan->updated_at ?? $laporan->created_at,
                    'icon' => '▤',
                    'color' => 'purple',
                ]);
            } elseif (in_array($status, ['temuan', 'tindak lanjut'])) {
                $activities->push([
                    'type' => 'temuan_tindak_lanjut',
                    'title' => 'Temuan perlu tindak lanjut',
                    'subtitle' => ($laporan->createdBy?->name ?? 'Pengawas').' · '.($laporan->dapur?->nama ?? 'Dapur'),
                    'time' => $laporan->updated_at ?? $laporan->created_at,
                    'icon' => '⚑',
                    'color' => 'yellow',
                ]);
            } else {
                // 'diperiksa' (default) atau status lain: laporan baru dikirim.
                $activities->push([
                    'type' => 'laporan_dikirim',
                    'title' => 'Laporan inspeksi dikirim',
                    'subtitle' => ($laporan->createdBy?->name ?? 'Pengawas').' · '.($laporan->dapur?->nama ?? 'Dapur'),
                    'time' => $laporan->created_at,
                    'icon' => '✓',
                    'color' => 'green',
                ]);
            }
        }
        foreach ($recentAnomalies as $anomaly) {
            $activities->push([
                'type' => 'anomali_cctv',
                'title' => 'Temuan anomali CCTV',
                'subtitle' => ($anomaly->cctv?->dapur?->nama ?? 'Dapur').' · '.($anomaly->cctv?->nama ?? 'Kamera'),
                'time' => $anomaly->created_at,
                'icon' => '▣',
                'color' => 'blue',
            ]);
        }
        $recentActivities = $activities->sortByDesc('time')->take(6)->values();

        return view('admin.dashboard', compact('stats', 'dapurs', 'pengawas', 'recentLaporans', 'recentAnomalies', 'previewCctvs', 'recentActivities'));
    }

    // Settings
    public function settings()
    {
        $settings = Settings::allKeyed();
        $general = Settings::group('general');

        // Filter office settings from general group
        $map = collect($general)->only(['office_lat', 'office_lng', 'office_address'])->toArray();

        // Filter branding settings from general group (they're stored in 'general' group in DB)
        $branding = collect($general)->only(['app_name_desktop', 'app_name_web', 'app_tagline'])->toArray();

        return view('admin.settings', compact('settings', 'branding', 'map', 'general'));
    }

    public function updateSettings(Request $request)
    {
        $validated = $request->validate([
            'app_name_desktop' => ['required', 'string', 'max:100'],
            'app_name_web' => ['required', 'string', 'max:100'],
            'app_tagline' => ['nullable', 'string', 'max:150'],
            'office_lat' => ['required', 'numeric', 'between:-90,90'],
            'office_lng' => ['required', 'numeric', 'between:-180,180'],
            'office_address' => ['nullable', 'string', 'max:255'],
        ]);

        Settings::set('app_name_desktop', $validated['app_name_desktop']);
        Settings::set('app_name_web', $validated['app_name_web']);
        Settings::set('app_tagline', $validated['app_tagline'] ?? '');
        Settings::set('office_lat', $validated['office_lat']);
        Settings::set('office_lng', $validated['office_lng']);
        Settings::set('office_address', $validated['office_address'] ?? '');

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Pengaturan berhasil disimpan.',
            ]);
        }

        return redirect()->route('admin.settings')->with('status', 'Pengaturan berhasil disimpan.');
    }

    // Profile Settings
    public function editProfile()
    {
        $user = auth()->user();

        return view('admin.settings.profile', compact('user'));
    }

    public function updateProfile(Request $request)
    {
        $user = auth()->user();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,'.$user->id],
            'password' => ['nullable', 'confirmed', Password::defaults()],
        ]);

        $update = [
            'name' => $validated['name'],
            'email' => $validated['email'],
        ];

        if ($validated['password'] ?? null) {
            $update['password'] = Hash::make($validated['password']);
        }

        $user->update($update);

        return redirect()->route('admin.settings.profile')->with('status', 'Profil berhasil diperbarui.');
    }
}
