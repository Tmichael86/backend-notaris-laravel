<?php

namespace Database\Seeders;

use App\Models\Group;
use Illuminate\Database\Seeder;

class GroupsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Group::create([
            'group_nama' => 'Super Admin',
            'group_jenis' => 'superadmin',
            'created_at' => date('Y-m-d H:i:s'),
            'created_by' => 1,
            'status' => 1,
        ]);

        Group::create([
            'group_nama' => 'Admin',
            'group_jenis' => 'user',
            'created_at' => date('Y-m-d H:i:s'),
            'created_by' => 1,
            'status' => 1,
        ]);

        Group::create([
            'group_nama' => 'Petugas',
            'group_jenis' => 'user',
            'created_at' => date('Y-m-d H:i:s'),
            'created_by' => 1,
            'status' => 1,
        ]);

        Group::create([
            'group_nama' => 'CS',
            'group_jenis' => 'user',
            'created_at' => date('Y-m-d H:i:s'),
            'created_by' => 1,
            'status' => 1,
        ]);
    }
}
