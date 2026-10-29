<?php

namespace App\Http\Controllers;

use App\Jobs\CctvAlertJob;
use App\Models\Cctv;
use App\Models\Dapur;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;

class CctvController extends Controller
{
    use AuthorizesRequests;

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $user = auth()->user();

        if (! $user->hasRole(['admin', 'manajerial', 'kepala_dapur', 'pengawas'])) {
            abort(403);
        }

        $dapurIds = $user->getAccessibleDapurIds();

        $cctvs = Cctv::with('dapur')
            ->whereIn('dapur_id', $dapurIds)
            ->latest()
            ->paginate(10);

        if ($user->isKepalaDapur()) {
            return view('kepala-dapur.cctvs.index', compact('cctvs'));
        } elseif ($user->isManajerial()) {
            return view('manajerial.cctvs.index', compact('cctvs'));
        } elseif ($user->isPengawas()) {
            return view('pengawas.cctvs.index', compact('cctvs'));
        }

        return view('admin.cctvs.index', compact('cctvs'));
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
            'ip_address' => ['nullable', 'string', 'max:45'],
            'rtsp_url' => ['nullable', 'string', 'max:500'],
            'hls_url' => ['nullable', 'string', 'max:500'],
            'status' => ['required', 'in:online,offline,maintenance'],
            'section' => ['nullable', 'string', 'max:255'],
            'camera_number' => ['nullable', 'integer', 'min:1'],
        ]);

        Cctv::create($validated);

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
            'ip_address' => ['nullable', 'string', 'max:45'],
            'rtsp_url' => ['nullable', 'string', 'max:500'],
            'hls_url' => ['nullable', 'string', 'max:500'],
            'status' => ['required', 'in:online,offline,maintenance'],
            'section' => ['nullable', 'string', 'max:255'],
            'camera_number' => ['nullable', 'integer', 'min:1'],
        ]);

        $oldStatus = $cctv->status;
        $cctv->update($validated);

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

        $cctv->delete();

        return redirect()->route('admin.cctvs.index')->with('status', 'CCTV berhasil dihapus.');
    }

    public function monitoring(Dapur $dapur)
    {
        $user = auth()->user();

        if (! $user->hasRole(['admin', 'manajerial', 'kepala_dapur', 'pengawas'])) {
            abort(403);
        }

        if (! $user->canAccessDapur($dapur->id)) {
            abort(403);
        }

        $dapur->load('cctvs');

        if ($user->isKepalaDapur()) {
            return view('kepala-dapur.cctvs.monitoring', compact('dapur'));
        } elseif ($user->isManajerial()) {
            return view('manajerial.cctvs.monitoring', compact('dapur'));
        } elseif ($user->isPengawas()) {
            return view('pengawas.cctvs.monitoring', compact('dapur'));
        }

        return view('admin.cctvs.monitoring', compact('dapur'));
    }

    public function monitoringDashboard()
    {
        $user = auth()->user();

        if (! $user->hasRole(['admin', 'manajerial', 'kepala_dapur', 'pengawas'])) {
            abort(403);
        }

        $dapurIds = $user->getAccessibleDapurIds();

        // Get stats
        $stats = [
            'total' => Cctv::whereIn('dapur_id', $dapurIds)->count(),
            'online' => Cctv::whereIn('dapur_id', $dapurIds)->where('status', 'online')->count(),
            'offline' => Cctv::whereIn('dapur_id', $dapurIds)->where('status', 'offline')->count(),
            'maintenance' => Cctv::whereIn('dapur_id', $dapurIds)->where('status', 'maintenance')->count(),
        ];

        // Get all CCTV with dapur relationship, ordered by dapur then camera_number
        $cctvs = Cctv::with('dapur')
            ->whereIn('dapur_id', $dapurIds)
            ->orderBy('dapur_id')
            ->orderBy('camera_number')
            ->get()
            ->groupBy('dapur_id');

        // Get dapurs for filter dropdown
        $dapurs = Dapur::whereIn('id', $dapurIds)->get(['id', 'nama']);

        if ($user->isKepalaDapur()) {
            return view('kepala-dapur.cctvs.monitoring-dashboard', compact('stats', 'cctvs', 'dapurs'));
        } elseif ($user->isManajerial()) {
            return view('manajerial.cctvs.monitoring-dashboard', compact('stats', 'cctvs', 'dapurs'));
        } elseif ($user->isPengawas()) {
            return view('pengawas.cctvs.monitoring-dashboard', compact('stats', 'cctvs', 'dapurs'));
        }

        return view('admin.cctvs.monitoring-dashboard', compact('stats', 'cctvs', 'dapurs'));
    }
}
