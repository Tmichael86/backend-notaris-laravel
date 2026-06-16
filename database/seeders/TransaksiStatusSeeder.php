<?php

namespace Database\Seeders;

use App\Models\TransaksiStatus;
use Illuminate\Database\Seeder;

class TransaksiStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        TransaksiStatus::create([
            'nama' => 'Baru',
            'created_at' => date('Y-m-d H:i:s'),
            'created_by' => 1,
            'status' => 1,
        ]);

        TransaksiStatus::create([
            'nama' => 'Dalam Proses',
            'created_at' => date('Y-m-d H:i:s'),
            'created_by' => 1,
            'status' => 1,
        ]);

        TransaksiStatus::create([
            'nama' => 'Selesai',
            'created_at' => date('Y-m-d H:i:s'),
            'created_by' => 1,
            'status' => 1,
        ]);
    }
}
