<?php

namespace App\Http\Controllers;

use App\Models\Absensi;
use App\Models\Cctv;
use App\Models\CctvAnomalyReport;
use App\Models\Dapur;
use App\Models\SpWarning;
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
        $query = User::with('registeredBy')->latest();

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
        return view('admin.users.create');
    }

    public function storeUser(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Password::defaults()],
            'role' => ['required', 'in:pengawas,manajerial,kepala_dapur'],
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'],
            'registered_by' => auth()->id(),
        ]);

        return redirect()->route('admin.users')->with('status', 'User berhasil dibuat.');
    }

    public function editUser(User $user)
    {
        // Admin tidak bisa edit diri sendiri melalui sini
        if ($user->id === auth()->id()) {
            abort(403, 'Tidak bisa mengedit akun sendiri.');
        }
        // Admin tidak bisa edit admin lain
        if ($user->isAdmin()) {
            abort(403, 'Tidak bisa mengedit admin lain.');
        }

        return view('admin.users.edit', compact('user'));
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
            'password' => ['nullable', 'confirmed', Password::defaults()],
        ]);

        $user->update(array_filter($validated, fn ($v) => $v !== null));

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

        return redirect()->route('admin.users')->with('status', 'User berhasil dihapus.');
    }

    // Dashboard Stats
    public function dashboard()
    {
        $stats = [
            'total_pengawas' => User::where('role', 'pengawas')->count(),
            'total_manajerial' => User::where('role', 'manajerial')->count(),
            'total_kepala_dapur' => User::where('role', 'kepala_dapur')->count(),
            'total_dapur' => Dapur::count(),
            'total_cctv' => Cctv::count(),
            'cctv_online' => Cctv::where('status', 'online')->count(),
            'cctv_offline' => Cctv::where('status', 'offline')->count(),
            'cctv_maintenance' => Cctv::where('status', 'maintenance')->count(),
            'absensi_hari_ini' => Absensi::whereDate('created_at', today())->count(),
            'sp_warnings_aktif' => SpWarning::where('status', 'aktif')->count(),
            'anomaly_reports_open' => CctvAnomalyReport::where('status', '!=', 'selesai')->count(),
        ];

        $recentAbsensi = Absensi::with('user', 'dapur')
            ->latest()
            ->limit(10)
            ->get();

        $recentAnomalies = CctvAnomalyReport::with('cctv.dapur', 'admin')
            ->latest()
            ->limit(10)
            ->get();

        return view('admin.dashboard', compact('stats', 'recentAbsensi', 'recentAnomalies'));
    }
}
