<?php

namespace Database\Seeders;

use App\Models\Sidebar;
use App\Models\SidebarAkses;
use Illuminate\Database\Seeder;

class SidebarAksesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // -- superadmin
        $sidebar = Sidebar::all();
        foreach ($sidebar as $val) {
            SidebarAkses::create([
                'sidebar_id' => $val->id,
                'group_id' => 1,
                'read' => 1,
                'create' => 1,
                'update' => 1,
                'delete' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'created_by' => 1,
            ]);
        }

        // -- admin
        $sidebar = Sidebar::whereIn('sidebar_kode', [
            'penghasilan',
            'pengeluaran',
            'piutang',
            'materai',
            'laporan',
        ])->get();

        foreach ($sidebar as $val) {
            SidebarAkses::create([
                'sidebar_id' => $val->id,
                'group_id' => 2,
                'read' => 1,
                'create' => 1,
                'update' => 1,
                'delete' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'created_by' => 1,
            ]);
        }

        // -- petugas
        $sidebar = Sidebar::whereIn('sidebar_kode', [
            'transaksi',
            'petugas',
            'pemohon'
        ])->get();

        foreach ($sidebar as $val) {
            SidebarAkses::create([
                'sidebar_id' => $val->id,
                'group_id' => 3,
                'read' => 1,
                'create' => 0,
                'update' => $val->sidebar_kode == 'transaksi' ? 1 : 0,
                'delete' => 0,
                'created_at' => date('Y-m-d H:i:s'),
                'created_by' => 1,
            ]);
        }

        // -- cs
        $sidebar = Sidebar::whereIn('sidebar_kode', [
            'transaksi',
            'petugas',
            'pemohon'
        ])->get();

        foreach ($sidebar as $val) {
            SidebarAkses::create([
                'sidebar_id' => $val->id,
                'group_id' => 4,
                'read' => 1,
                'create' => $val->sidebar_kode == 'transaksi' ? 1 : 0,
                'update' => 0,
                'delete' => 0,
                'created_at' => date('Y-m-d H:i:s'),
                'created_by' => 1,
            ]);
        }
    }
}
