<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $dataUser = [
            [
                'group_id' => 1,
                'username' => 'superadmin',
                'password' => Hash::make('password@123'),
                'email' => 'superadmin@gmail.com',
                'nama' => 'Super Admin',
                'no_telp' => '081222333444',
                'alamat' => 'Jl. Unknown',
                'created_at' => date('Y-m-d H:i:s'),
                'created_by' => 1,
                'status' => 1,
            ],
            [
                'group_id' => 2,
                'username' => 'admin',
                'password' => Hash::make('password@123'),
                'email' => 'admin@gmail.com',
                'nama' => 'Admin',
                'no_telp' => '081222333444',
                'alamat' => 'Jl. Unknown',
                'created_at' => date('Y-m-d H:i:s'),
                'created_by' => 1,
                'status' => 1,
            ],
            [
                'group_id' => 3,
                'username' => 'petugas',
                'password' => Hash::make('password@123'),
                'email' => 'petugas@gmail.com',
                'nama' => 'Petugas',
                'no_telp' => '081222333444',
                'alamat' => 'Jl. Unknown',
                'created_at' => date('Y-m-d H:i:s'),
                'created_by' => 1,
                'status' => 1,
            ],
            [
                'group_id' => 4,
                'username' => 'cs',
                'password' => Hash::make('password@123'),
                'email' => 'cs@gmail.com',
                'nama' => 'CS',
                'no_telp' => '081222333444',
                'alamat' => 'Jl. Unknown',
                'created_at' => date('Y-m-d H:i:s'),
                'created_by' => 1,
                'status' => 1,
            ]
        ];

        User::insert($dataUser);
    }
}
