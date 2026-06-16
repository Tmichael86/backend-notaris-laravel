<?php

namespace Database\Seeders;

use App\Models\Petugas;
use App\Models\User;
use Illuminate\Database\Seeder;

class PetugasSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $users = User::all();
        foreach ($users as $key => $val) {
            Petugas::create([
                'nik' => "350501010100000$key",
                'nama' => $val->nama,
                'alamat' => $val->alamat,
                'tempat_lahir' => 'Blitar',
                'tanggal_lahir' => '1994-07-28',
                'jenis_kelamin' => 1,
                'no_telp' => '081222333444',
                'email' => $val->email,
                'user_id' => $val->id,
                'created_at' => date('Y-m-d H:i:s'),
                'created_by' => 1,
                'status' => 1
            ]);
        }
    }
}
