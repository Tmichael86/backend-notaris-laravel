<?php

namespace Database\Seeders;

use App\Models\Sidebar;
use Illuminate\Database\Seeder;

class SidebarSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Sidebar::create([
            'sidebar_parent_id' => 0,
            'sidebar_nama' => 'Transaksi',
            'sidebar_route' => '/administrator/transaksi',
            'sidebar_kode' => 'transaksi',
            'sidebar_index' => '1',
            'sidebar_icon' => 'fas fa-hand-holding-usd',
            'created_at' => date('Y-m-d H:i:s'),
            'created_by' => 1,
            'status' => 1,
        ]);
        Sidebar::create([
            'sidebar_parent_id' => 0,
            'sidebar_nama' => 'Monitoring',
            'sidebar_route' => '/administrator/monitoring',
            'sidebar_kode' => 'monitoring',
            'sidebar_index' => '2',
            'sidebar_icon' => 'fas fa-desktop',
            'created_at' => date('Y-m-d H:i:s'),
            'created_by' => 1,
            'status' => 1,
        ]);

        Sidebar::create([
            'sidebar_parent_id' => 0,
            'sidebar_nama' => 'Pengeluaran',
            'sidebar_route' => '/administrator/pengeluaran',
            'sidebar_kode' => 'pengeluaran',
            'sidebar_index' => '4',
            'sidebar_icon' => 'fas fa-file-invoice-dollar',
            'created_at' => date('Y-m-d H:i:s'),
            'created_by' => 1,
            'status' => 1,
        ]);
        Sidebar::create([
            'sidebar_parent_id' => 0,
            'sidebar_nama' => 'Piutang',
            'sidebar_route' => '/administrator/piutang',
            'sidebar_kode' => 'piutang',
            'sidebar_index' => '5',
            'sidebar_icon' => 'fas fa-file-invoice',
            'created_at' => date('Y-m-d H:i:s'),
            'created_by' => 1,
            'status' => 1,
        ]);
        Sidebar::create([
            'sidebar_parent_id' => 0,
            'sidebar_nama' => 'Penghasilan',
            'sidebar_route' => '/administrator/penghasilan',
            'sidebar_kode' => 'penghasilan',
            'sidebar_index' => '6',
            'sidebar_icon' => 'fas fa-file-invoice-dollar',
            'created_at' => date('Y-m-d H:i:s'),
            'created_by' => 1,
            'status' => 1,
        ]);
        $data_master = Sidebar::create([
            'sidebar_parent_id' => 0,
            'sidebar_nama' => 'Master',
            'sidebar_route' => '#',
            'sidebar_kode' => 'master',
            'sidebar_index' => '98',
            'sidebar_icon' => 'fa fa-book',
            'created_at' => date('Y-m-d H:i:s'),
            'created_by' => 1,
            'status' => 1,
        ]);

        Sidebar::create([
            'sidebar_parent_id' => $data_master->id,
            'sidebar_nama' => 'Pekerjaan Notaris',
            'sidebar_route' => '/administrator/master/pekerjaan_notaris',
            'sidebar_kode' => 'pekerjaan_notaris',
            'sidebar_index' => '1',
            'sidebar_icon' => 'fas fa-file',
            'created_at' => date('Y-m-d H:i:s'),
            'created_by' => 1,
            'status' => 1,
        ]);

        Sidebar::create([
            'sidebar_parent_id' => $data_master->id,
            'sidebar_nama' => 'Pekerjaan PPAT',
            'sidebar_route' => '/administrator/master/pekerjaan_ppat',
            'sidebar_kode' => 'pekerjaan_ppat',
            'sidebar_index' => '2',
            'sidebar_icon' => 'fas fa-file',
            'created_at' => date('Y-m-d H:i:s'),
            'created_by' => 1,
            'status' => 1,
        ]);

        Sidebar::create([
            'sidebar_parent_id' => $data_master->id,
            'sidebar_nama' => 'Kategori Pekerjaan',
            'sidebar_route' => '/administrator/master/kategori_pekerjaan',
            'sidebar_kode' => 'kategori_pekerjaan',
            'sidebar_index' => '3',
            'sidebar_icon' => 'fas fa-filter',
            'created_at' => date('Y-m-d H:i:s'),
            'created_by' => 1,
            'status' => 1,
        ]);

        Sidebar::create([
            'sidebar_parent_id' => $data_master->id,
            'sidebar_nama' => 'Jenis Pengeluaran',
            'sidebar_route' => '/administrator/master/jenis_pengeluaran',
            'sidebar_kode' => 'jenis_pengeluaran',
            'sidebar_index' => '4',
            'sidebar_icon' => 'fas fa-filter',
            'created_at' => date('Y-m-d H:i:s'),
            'created_by' => 1,
            'status' => 1,
        ]);

        Sidebar::create([
            'sidebar_parent_id' => $data_master->id,
            'sidebar_nama' => 'Petugas',
            'sidebar_route' => '/administrator/master/petugas',
            'sidebar_kode' => 'petugas',
            'sidebar_index' => '5',
            'sidebar_icon' => 'fas fa-user-tie',
            'created_at' => date('Y-m-d H:i:s'),
            'created_by' => 1,
            'status' => 1,
        ]);

        Sidebar::create([
            'sidebar_parent_id' => $data_master->id,
            'sidebar_nama' => 'Pemohon',
            'sidebar_route' => '/administrator/master/pemohon',
            'sidebar_kode' => 'pemohon',
            'sidebar_index' => '6',
            'sidebar_icon' => 'fas fa-users',
            'created_at' => date('Y-m-d H:i:s'),
            'created_by' => 1,
            'status' => 1,
        ]);

        $laporan = Sidebar::create([
            'sidebar_parent_id' => 0,
            'sidebar_nama' => 'Laporan',
            'sidebar_route' => '#',
            'sidebar_kode' => 'laporan',
            'sidebar_index' => '99',
            'sidebar_icon' => 'fas fa-flag',
            'created_at' => date('Y-m-d H:i:s'),
            'created_by' => 1,
            'status' => 1,
        ]);

        Sidebar::create([
            'sidebar_parent_id' => $laporan->id,
            'sidebar_nama' => 'Materai',
            'sidebar_route' => '/administrator/laporan/materai',
            'sidebar_kode' => 'materai',
            'sidebar_index' => '1',
            'sidebar_icon' => 'fas fa-plus',
            'created_at' => date('Y-m-d H:i:s'),
            'created_by' => 1,
            'status' => 1,
        ]);

        Sidebar::create([
            'sidebar_parent_id' => $laporan->id,
            'sidebar_nama' => 'Pendapatan',
            'sidebar_route' => '/administrator/laporan/pendapatan',
            'sidebar_kode' => 'pendapatan',
            'sidebar_index' => '2',
            'sidebar_icon' => 'fas fa-bandcamp',
            'created_at' => date('Y-m-d H:i:s'),
            'created_by' => 1,
            'status' => 1,
        ]);

        $parent = Sidebar::create([
            'sidebar_parent_id' => 0,
            'sidebar_nama' => 'System',
            'sidebar_route' => '#',
            'sidebar_kode' => 'system',
            'sidebar_index' => '99',
            'sidebar_icon' => 'fa fa-cogs',
            'created_at' => date('Y-m-d H:i:s'),
            'created_by' => 1,
            'status' => 1,
        ]);

        Sidebar::create([
            'sidebar_parent_id' => $parent->id,
            'sidebar_nama' => 'Users',
            'sidebar_route' => '/administrator/users',
            'sidebar_kode' => 'users',
            'sidebar_index' => '1',
            'sidebar_icon' => 'fas fa-users',
            'created_at' => date('Y-m-d H:i:s'),
            'created_by' => 1,
            'status' => 1,
        ]);

        Sidebar::create([
            'sidebar_parent_id' => $parent->id,
            'sidebar_nama' => 'Groups',
            'sidebar_route' => '/administrator/groups',
            'sidebar_kode' => 'groups',
            'sidebar_index' => '1',
            'sidebar_icon' => 'fas fa-layer-group',
            'created_at' => date('Y-m-d H:i:s'),
            'created_by' => 1,
            'status' => 1,
        ]);

        Sidebar::create([
            'sidebar_parent_id' => $parent->id,
            'sidebar_nama' => 'Sidebars',
            'sidebar_route' => '/administrator/sidebars',
            'sidebar_kode' => 'sidebars',
            'sidebar_index' => '2',
            'sidebar_icon' => 'fa fa-list',
            'created_at' => date('Y-m-d H:i:s'),
            'created_by' => 1,
            'status' => 1,
        ]);

        Sidebar::create([
            'sidebar_parent_id' => $parent->id,
            'sidebar_nama' => 'Konfigurasi Umum',
            'sidebar_route' => '/administrator/konfigurasi',
            'sidebar_kode' => 'konfigurasi',
            'sidebar_index' => '3',
            'sidebar_icon' => 'fa fa-cog',
            'created_at' => date('Y-m-d H:i:s'),
            'created_by' => 1,
            'status' => 1,
        ]);

    }
}
