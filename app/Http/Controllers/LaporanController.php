<?php

namespace App\Http\Controllers;

use App\Models\Laporan;
use App\Models\LaporanPhoto;
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
            // 5 indikator photos + 1 selfie dapur
            'photo_indikator_1' => 'required|string',
            'photo_indikator_2' => 'required|string',
            'photo_indikator_3' => 'required|string',
            'photo_indikator_4' => 'required|string',
            'photo_indikator_5' => 'required|string',
            'photo_selfie' => 'required|string',
            'latitude' => 'required|array|min:6|max:6',
            'latitude.*' => 'required|numeric|between:-90,90',
            'longitude' => 'required|array|min:6|max:6',
            'longitude.*' => 'required|numeric|between:-180,180',
            'address' => 'required|array|min:6|max:6',
            // 5 hasil pemeriksaan indikator (wajib dipilih)
            'hasil' => 'required|array|size:5',
            'hasil.*' => 'required|in:diperiksa,temuan_sedang,temuan_kritis',
        ]);

        // Create laporan first (need ID for folder)
        $laporan = Laporan::create([
            'dapur_id' => $validated['dapur_id'],
            'status' => $validated['status'],
            'catatan' => $validated['catatan'] ?? null,
        ]);

        // Store each photo with its GPS coordinates and indikator
        $photoFields = [
            'photo_indikator_1' => 'Kebersihan dan sanitasi',
            'photo_indikator_2' => 'Penyimpanan bahan',
            'photo_indikator_3' => 'Proses pengolahan',
            'photo_indikator_4' => 'Distribusi makanan',
            'photo_indikator_5' => 'Peralatan dan fasilitas',
            'photo_selfie' => 'Selfie di depan dapur',
        ];

        $photos = [];
        $index = 0;
        foreach ($photoFields as $fieldName => $indikatorName) {
            $photoPath = $this->storeSelfie($validated[$fieldName], $laporan->id);
            $photos[] = LaporanPhoto::create([
                'laporan_id' => $laporan->id,
                'photo_path' => $photoPath,
                'indikator' => $indikatorName,
                'latitude' => $validated['latitude'][$index] ?? null,
                'longitude' => $validated['longitude'][$index] ?? null,
                'address' => $validated['address'][$index] ?? null,
            ]);
            $index++;
        }

        return redirect()->route('pengawas.laporan.index')
            ->with('success', 'Laporan berhasil dikirim ('.count($photos).' foto).');
    }

    public function show(Laporan $laporan)
    {
        $laporan->load(['dapur', 'photos']);

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
        $laporan->save();

        return redirect()->route('admin.laporan.show', $laporan->id)
            ->with('success', 'Laporan berhasil diperbarui.');
    }

    private function storeSelfie(string $dataUrl, int $laporanId): string
    {
        $parts = explode(';base64,', $dataUrl);
        $ext = str_replace('data:image/', '', $parts[0]);
        $data = base64_decode($parts[1]);

        $folder = 'laporans/'.$laporanId.'/photos';
        $filename = 'photo-'.now()->format('YmdHis').'-'.str()->random(6).'.'.$ext;

        Storage::disk('public')->makeDirectory($folder);
        Storage::disk('public')->put($folder.'/'.$filename, $data);

        return $folder.'/'.$filename;
    }
}