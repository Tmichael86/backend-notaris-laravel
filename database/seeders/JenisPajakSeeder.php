<?php

namespace Database\Seeders;

use App\Models\JenisPajak;
use Illuminate\Database\Seeder;

class JenisPajakSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        JenisPajak::query()->create([
            'nama_pajak' => 'Balik Nama Waris',
            'created_at' => now(),
            'created_by' => 1,
            'status' => 1,
        ]);

        JenisPajak::query()->create([
            'nama_pajak' => 'Biaya Hibah',
            'created_at' => now(),
            'created_by' => 1,
            'status' => 1,
        ]);

        JenisPajak::query()->create([
            'nama_pajak' => 'AJB',
            'created_at' => now(),
            'created_by' => 1,
            'status' => 1,
        ]);

        JenisPajak::query()->create([
            'nama_pajak' => 'Tukar Guling',
            'created_at' => now(),
            'created_by' => 1,
            'status' => 1,
        ]);

        JenisPajak::query()->create([
            'nama_pajak' => 'Biaya Jasa APHB',
            'created_at' => now(),
            'created_by' => 1,
            'status' => 1,
        ]);

        JenisPajak::query()->create([
            'nama_pajak' => 'APHT',
            'created_at' => now(),
            'created_by' => 1,
            'status' => 1
        ]);
    }
}
