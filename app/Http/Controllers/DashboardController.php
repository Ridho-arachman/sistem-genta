<?php

namespace App\Http\Controllers;

use App\Models\Cctv;
use App\Models\CctvAnomalyReport;
use App\Models\Dapur;
use App\Models\Laporan;
use App\Models\SpWarning;
use App\Models\User;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Arahkan /dashboard ke beranda per peran.
     */
    public function index(Request $request)
    {
        $user = $request->user();

        return match (true) {
            $user->isAdmin() => redirect()->route('admin.dashboard'),
            $user->isPengawas() => redirect()->route('pengawas.dashboard'),
            $user->isManajerial() => redirect()->route('manajerial.dashboard'),
            $user->isKepalaDapur() => redirect()->route('kepala-dapur.dashboard'),
            default => abort(403),
        };
    }

    public function pengawas(Request $request)
    {
        $user = $request->user();

        $dapurs = Dapur::forUser($user)
            ->withCount(['cctvs', 'cctvs as cctv_online_count' => fn ($q) => $q->where('status', 'online')])
            ->get();

        $stats = [
            'total_dapur' => $dapurs->count(),
            'total_cctv' => $dapurs->sum('cctvs_count'),
            'cctv_online' => $dapurs->sum('cctv_online_count'),
            'laporan_hari_ini' => Laporan::where('created_by', $user->id)->whereDate('created_at', today())->count(),
            'temuan_terbuka' => CctvAnomalyReport::whereHas('cctv.dapur', fn ($q) => $q->whereIn('id', $dapurs->pluck('id')))->where('status', '!=', 'selesai')->count(),
        ];

        return view('pengawas.dashboard', compact('dapurs', 'stats'));
    }

    public function manajerial()
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
            'laporan_hari_ini' => Laporan::whereDate('created_at', today())->count(),
            'sp_warnings_aktif' => SpWarning::where('status', 'aktif')->count(),
            'anomaly_reports_open' => CctvAnomalyReport::where('status', '!=', 'selesai')->count(),
        ];

        $dapurs = Dapur::with('cctvs')->get();
        $recentLaporans = Laporan::with('createdBy', 'dapur')->latest()->limit(10)->get();

        return view('manajerial.dashboard', compact('stats', 'dapurs', 'recentLaporans'));
    }

    public function kepalaDapur(Request $request)
    {
        $user = $request->user();
        $dapurs = Dapur::forUser($user)->with('cctvs')->get();

        return view('kepala-dapur.dashboard', compact('dapurs'));
    }
}
