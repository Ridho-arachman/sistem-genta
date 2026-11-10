<?php

namespace App\Http\Controllers;

use App\Models\Laporan;
use App\Models\LaporanHasil;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class LaporanController extends Controller
{
    public function index()
    {
        $laporans = Laporan::with(['dapur', 'createdBy'])->latest()->paginate(15);

        $badgeMap = [
            'diperiksa' => 'badge-success',
            'temuan' => 'badge-danger',
            'tindak lanjut' => 'badge-warning',
            'selesai' => 'badge-success',
        ];

        $laporanRows = $laporans->map(function ($l) use ($badgeMap) {
            return [
                'id' => $l->id,
                'tanggal' => $l->created_at->format('d M Y'),
                'lokasi' => $l->dapur->nama ?? '-',
                'pengawas' => $l->createdBy->name ?? '-',
                'status' => ucfirst(str_replace('_', ' ', $l->status)),
                'badgeClass' => $badgeMap[$l->status] ?? 'badge-gray',
                'date' => $l->created_at->format('Y-m-d'),
            ];
        })->toArray();

        return view('admin.laporan.index', compact('laporans', 'laporanRows'));
    }

    public function create()
    {
        return view('pengawas.laporan.create');
    }

    public function store(Request $request)
    {
        $user = $request->user();

        // Validasi dapur harus milik pengawas yang login
        $dapurRule = $user->isPengawas()
            ? 'required|exists:dapurs,id|in:'.implode(',', $user->getAccessibleDapurIds())
            : 'required|exists:dapurs,id';

        $validated = $request->validate([
            'dapur_id' => $dapurRule,
            'status' => 'required|in:diperiksa,temuan,tindak lanjut,selesai',
            'catatan' => 'nullable|string',
            // Satu foto tunggal (selfie/foto utama)
            'selfie_photo' => 'required|string',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'address' => 'required|string',
            // 5 hasil indikator (wajib diisi semua)
            'hasil' => 'required|array|size:5',
            'hasil.*' => 'required|in:diperiksa,temuan_sedang,temuan_kritis',
        ]);

        // Simpan foto selfie sebagai file di storage (bukan base64 di DB)
        $selfiePhoto = $validated['selfie_photo'];
        $photoPath = null;
        if (str_starts_with($selfiePhoto, 'data:')) {
            [$type, $data] = explode(';', $selfiePhoto, 2);
            [, $data] = explode(',', $data, 2);
            $data = base64_decode($data);
            $extension = 'jpg';
            if (preg_match('/image\/(\w+)/', $type ?? '', $m)) {
                $extension = $m[1];
            }
            $filename = 'laporan-selfies/'.uniqid('lpr_', true).'.'.$extension;
            Storage::disk('public')->put($filename, $data);
            $photoPath = $filename;
        }

        // Create laporan first (need ID for folder)
        $laporan = Laporan::create([
            'dapur_id' => $validated['dapur_id'],
            'status' => $validated['status'],
            'catatan' => $validated['catatan'] ?? null,
            'selfie_photo' => $photoPath,
            'latitude' => $validated['latitude'],
            'longitude' => $validated['longitude'],
            'address' => $validated['address'],
            'created_by' => $user->id,
            'updated_by' => $user->id,
        ]);

        // Store hasil pemeriksaan per indikator (5 indikator tetap)
        $indikators = [
            'Kebersihan dan sanitasi',
            'Penyimpanan bahan',
            'Proses pengolahan',
            'Distribusi makanan',
            'Peralatan dan fasilitas',
        ];

        foreach ($indikators as $index => $indikatorName) {
            LaporanHasil::create([
                'laporan_id' => $laporan->id,
                'indikator' => $indikatorName,
                'hasil' => $validated['hasil'][$index] ?? 'diperiksa',
                'created_by' => $user->id,
                'updated_by' => $user->id,
            ]);
        }

        return redirect()->route('pengawas.laporan.index')
            ->with('success', 'Laporan berhasil dikirim.');
    }

    public function show(Laporan $laporan)
    {
        $user = auth()->user();

        // Pengawas hanya boleh lihat laporan buatan sendiri
        if ($user->isPengawas() && $laporan->created_by !== $user->id) {
            abort(403);
        }

        $laporan->load(['dapur', 'createdBy', 'hasil']);

        if ($user->isPengawas()) {
            return view('pengawas.laporan.show', compact('laporan'));
        }

        return view('admin.laporan.show', compact('laporan'));
    }

    public function update(Request $request, Laporan $laporan)
    {
        $validated = $request->validate([
            'status' => 'required|in:diperiksa,temuan,tindak lanjut,selesai',
            'catatan' => 'nullable|string',
        ]);

        $laporan->status = $validated['status'];
        $laporan->catatan = $validated['catatan'] ?? null;
        $laporan->updated_by = $request->user()->id;
        $laporan->save();

        return redirect()->route('admin.laporan.show', $laporan->id)
            ->with('success', 'Laporan berhasil diperbarui.');
    }
}
