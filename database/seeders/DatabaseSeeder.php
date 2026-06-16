<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call([
            UsersSeeder::class,
            GroupsSeeder::class,
            SidebarSeeder::class,
            SidebarAksesSeeder::class,
            KategoriPekerjaanSeeder::class,
            JenisKelaminSeeder::class,
            JenisPekerjaanSeeder::class,
            JenisPembayaranSeeder::class,
            TransaksiStatusSeeder::class,
            PekerjaanNotarisSeeder::class,
            PekerjaanPPATSeeder::class,
            PetugasSeeder::class,
            JenisPajakSeeder::class,
            JenisPengeluaranSeeder::class,
            KonfigurasiUmumSeeder::class,
            MateraiSeeder::class,
        ]);
    }
}
