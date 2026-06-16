<?php

namespace Database\Seeders;

use App\Models\JenisPekerjaan;
use Illuminate\Database\Seeder;

class JenisPekerjaanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        JenisPekerjaan::create([
            'nama' => 'Notaris',
            'created_at' => date('Y-m-d H:i:s'),
            'created_by' => 1,
            'status' => 1,
        ]);

        JenisPekerjaan::create([
            'nama' => 'PPAT',
            'created_at' => date('Y-m-d H:i:s'),
            'created_by' => 1,
            'status' => 1,
        ]);

    }
}
