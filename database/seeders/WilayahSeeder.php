<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class WilayahSeeder extends Seeder
{
    public function run(): void
    {
        $path = database_path('data/wilayah.sql.gz');

        if (!File::exists($path)) {
            $this->command->error("Wilayah data file not found at {$path}");
            return;
        }

        $handle = gzopen($path, 'rb');

        if ($handle === false) {
            $this->command->error("Failed to open gzip file.");
            return;
        }

        $this->command->info("Importing wilayah data...");

        // The dump is MySQL-flavoured, so instead of executing it as raw SQL we stream it
        // and insert the ('kode','nama') rows in batches. Works on MySQL and Postgres alike.
        DB::table('wilayah')->truncate();

        $batch = [];
        $total = 0;

        while (($line = gzgets($handle)) !== false) {
            if (!preg_match("/^\('([^']+)','((?:[^']|'')*)'\)[,;]?\s*$/", $line, $m)) {
                continue;
            }

            $batch[] = ['kode' => $m[1], 'nama' => str_replace("''", "'", $m[2])];

            if (count($batch) === 500) {
                DB::table('wilayah')->insert($batch);
                $total += count($batch);
                $batch = [];
            }
        }

        gzclose($handle);

        if ($batch) {
            DB::table('wilayah')->insert($batch);
            $total += count($batch);
        }

        $this->command->info("Wilayah table seeded successfully! ({$total} rows)");
    }
}
