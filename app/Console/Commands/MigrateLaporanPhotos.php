<?php

namespace App\Console\Commands;

use App\Models\Laporan;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

#[Signature('laporan:migrate-photos')]
#[Description('Pindahkan foto selfie base64 dari DB ke file storage/public')]
class MigrateLaporanPhotos extends Command
{
    public function handle(): int
    {
        $laporans = Laporan::where('selfie_photo', 'like', 'data:%')->get();

        if ($laporans->isEmpty()) {
            $this->info('Tidak ada foto base64 di DB yang perlu dipindahkan.');

            return self::SUCCESS;
        }

        $this->withProgressBar($laporans, function ($laporan) {
            $photo = $laporan->selfie_photo;
            [$type, $data] = explode(';', $photo, 2);
            [, $data] = explode(',', $data, 2);
            $data = base64_decode($data);

            $extension = 'jpg';
            if (preg_match('/image\/(\w+)/', $type ?? '', $m)) {
                $extension = $m[1];
            }

            $filename = 'laporan-selfies/'.uniqid('lpr_', true).'.'.$extension;
            Storage::disk('public')->put($filename, $data);

            $laporan->update(['selfie_photo' => $filename]);
        });

        $this->info("\nBerhasil memindahkan {$laporans->count()} foto ke storage.");

        return self::SUCCESS;
    }
}
