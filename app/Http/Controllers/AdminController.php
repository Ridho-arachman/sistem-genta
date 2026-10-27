<?php

namespace App\Http\Controllers;

use App\Models\Absensi;
use App\Models\Cctv;
use App\Models\CctvAnomalyReport;
use App\Models\Dapur;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class AdminController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:admin']);
    }

    // User Management
    public function users(Request $request)
    {
        $query = User::with(['registeredBy', 'dapur', 'dapurs', 'creator', 'updater', 'deactivator'])->latest();

        // Default: only active users, unless include_deactivated filter is set
        if (! $request->boolean('include_deactivated')) {
            $query->active();
        }

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
            'lokasi' => $d->lokasi,
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

            // Check if dapur already has a kepala_dapur
            $existingKepala = User::where('role', 'kepala_dapur')
                ->where('dapur_id', $validated['dapur_id'])
                ->first();
            if ($existingKepala) {
                return back()->withErrors([
                    'dapur_id' => 'Dapur ini sudah memiliki Kepala Dapur: '.$existingKepala->name.'. Menimpah akan mengubah assignment.',
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
            'is_active' => true,
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

        $user->load(['creator', 'updater', 'deactivator']);

        $dapurs = Dapur::orderBy('nama')->with('kepalaDapur')->get();
        $dapurData = $dapurs->map(fn ($d) => [
            'id' => $d->id,
            'nama' => $d->nama,
            'lokasi' => $d->lokasi,
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
            'is_active' => ['sometimes', 'boolean'],
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
                    'dapur_id' => 'Dapur ini sudah memiliki Kepala Dapur: '.$existingKepala->name.'. Menimpah akan mengubah assignment.',
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

        $user->update(array_filter([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'role' => $validated['role'],
            'dapur_id' => $validated['dapur_id'] ?? null,
            'is_active' => $validated['is_active'] ?? null,
        ]));

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

    // Dashboard Stats
    public function dashboard()
    {
        $totalCctv = Cctv::count();
        $cctvOnline = Cctv::where('status', 'online')->count();
        $laporanHariIni = CctvAnomalyReport::whereDate('created_at', today())->count();
        $laporanHariIniSelesai = CctvAnomalyReport::whereDate('created_at', today())->where('status', 'selesai')->count();

        $stats = [
            'total_dapur' => Dapur::count(),
            'total_cctv' => $totalCctv,
            'cctv_online' => $cctvOnline,
            'anomaly_reports_open' => CctvAnomalyReport::where('status', '!=', 'selesai')->count(),
            'laporan_hari_ini' => $laporanHariIni,
            'laporan_hari_ini_selesai' => $laporanHariIniSelesai,
            'total_pengawas' => User::where('role', 'pengawas')->count(),
            'total_manajerial' => User::where('role', 'manajerial')->count(),
            'total_kepala_dapur' => User::where('role', 'kepala_dapur')->count(),
            'pengawas_without_dapur' => User::where('role', 'pengawas')
                ->whereDoesntHave('dapurs')
                ->count(),
            'kepala_dapur_without_dapur' => User::where('role', 'kepala_dapur')
                ->whereNull('dapur_id')
                ->count(),
        ];

        $dapurs = Dapur::with(['cctvs', 'users' => function ($q) {
            $q->where('role', 'kepala_dapur');
        }])->get();

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

        $recentAbsensi = Absensi::with('user', 'dapur')
            ->latest()
            ->limit(10)
            ->get();

        $recentAnomalies = CctvAnomalyReport::with('cctv.dapur', 'admin')
            ->latest()
            ->limit(10)
            ->get();

        return view('admin.dashboard', compact('stats', 'dapurs', 'pengawas', 'recentAbsensi', 'recentAnomalies'));
    }
}
