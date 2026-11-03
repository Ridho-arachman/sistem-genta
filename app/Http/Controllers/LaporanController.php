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
        $laporans = Laporan::with('dapur')->latest()->paginate(15);

        return view('admin.laporan.index', compact('laporans'));
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
            ? 'required|exists:dapurs,id|in:' . implode(',', $user->getAccessibleDapurIds())
            : 'required|exists:dapurs,id';

        $validated = $request->validate([
            'dapur_id' => $dapurRule,
            'kategori' => 'nullable|string',
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

        // Create laporan first (need ID for folder)
        $laporan = Laporan::create([
            'dapur_id' => $validated['dapur_id'],
            'status' => $validated['status'],
            'catatan' => $validated['catatan'] ?? null,
            'selfie_photo' => $validated['selfie_photo'],
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
        $laporan->load(['dapur']);

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
