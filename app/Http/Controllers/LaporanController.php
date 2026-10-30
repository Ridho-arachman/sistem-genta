<?php

namespace App\Http\Controllers;

use App\Models\Laporan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class LaporanController extends Controller
{
    public function index()
    {
        $laporans = Laporan::with('dapur')->latest()->paginate(15);

        return view('admin.laporan.index', compact('laporans'));
    }

    public function show(Laporan $laporan)
    {
        $laporan->load('dapur');

        return view('admin.laporan.show', compact('laporan'));
    }

    public function update(Request $request, Laporan $laporan)
    {
        $validated = $request->validate([
            'status' => 'required|in:diperiksa,temuan,tindak lanjut,selesai',
            'catatan' => 'nullable|string',
            'selfie_photo' => 'nullable|string', // base64 data URL
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
        ]);

        $laporan->status = $validated['status'];
        $laporan->catatan = $validated['catatan'] ?? null;
        $laporan->latitude = $validated['latitude'];
        $laporan->longitude = $validated['longitude'];

        // Handle selfie photo upload (base64 -> file)
        if (! empty($validated['selfie_photo'])) {
            $path = $this->storeSelfie($validated['selfie_photo'], $laporan->id);
            $laporan->selfie_photo = $path;
        }

        $laporan->save();

        return redirect()->route('admin.laporan.show', $laporan->id)
            ->with('success', 'Laporan berhasil diperbarui.');
    }

    private function storeSelfie(string $dataUrl, int $laporanId): string
    {
        $parts = explode(';base64,', $dataUrl);
        $ext = str_replace('data:image/', '', $parts[0]);
        $data = base64_decode($parts[1]);

        $folder = 'laporans/'.$laporanId;
        $filename = 'selfie-'.now()->format('YmdHis').'.'.$ext;

        Storage::disk('public')->makeDirectory($folder);
        Storage::disk('public')->put($folder.'/'.$filename, $data);

        return $folder.'/'.$filename;
    }
}
