<?php

namespace Database\Seeders;

use App\Models\JenisKelamin;
use Illuminate\Database\Seeder;

class JenisKelaminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        JenisKelamin::create([
            'nama' => 'Laki-laki',
            'created_at' => date('Y-m-d H:i:s'),
            'created_by' => 1,
            'status' => 1,
        ]);

        JenisKelamin::create([
            'nama' => 'Perempuan',
            'created_at' => date('Y-m-d H:i:s'),
            'created_by' => 1,
            'status' => 1,
        ]);
    }
}
