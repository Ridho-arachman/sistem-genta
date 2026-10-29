<?php

namespace Database\Seeders;

use App\Models\Cctv;
use App\Models\Dapur;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CctvSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Only seed if no CCTV exists (idempotent for local dev)
        if (Cctv::count() > 0) {
            $this->command->info('CCTV already seeded, skipping.');
            return;
        }

        $dapurs = Dapur::all();

        if ($dapurs->isEmpty()) {
            $this->command->warn('No dapurs found. Run DapurSeeder first.');
            return;
        }

        $totalCreated = 0;

        foreach ($dapurs as $dapur) {
            // Random 3-7 CCTV per dapur
            $count = fake()->numberBetween(3, 7);

            Cctv::factory()
                ->count($count)
                ->forDapur($dapur)
                ->realistic()
                ->create();

            $totalCreated += $count;
            $this->command->info("Created {$count} CCTV for {$dapur->nama}");
        }

        $this->command->info("Total CCTV seeded: {$totalCreated} across {$dapurs->count()} dapur(s).");

        // Show summary
        $summary = Cctv::selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status')
            ->toArray();

        $this->command->table(
            ['Status', 'Count'],
            collect($summary)->map(fn ($count, $status) => [$status, $count])->values()->toArray()
        );
    }
}