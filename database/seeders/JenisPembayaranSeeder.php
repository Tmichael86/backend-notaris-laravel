<?php

namespace Database\Seeders;

use App\Models\JenisPembayaran;
use Illuminate\Database\Seeder;

class JenisPembayaranSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        JenisPembayaran::create([
            'nama' => 'Cash',
            'created_at' => date('Y-m-d H:i:s'),
            'created_by' => 1,
            'status' => 1,
        ]);

        JenisPembayaran::create([
            'nama' => 'Debit/Transfer',
            'created_at' => date('Y-m-d H:i:s'),
            'created_by' => 1,
            'status' => 1,
        ]);
    }
}
