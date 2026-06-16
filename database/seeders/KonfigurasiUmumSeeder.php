<?php

namespace Database\Seeders;

use App\Http\Libraries\System;
use App\Models\KonfigurasiUmum;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class KonfigurasiUmumSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        KonfigurasiUmum::query()
            ->create(
                System::crudIdentity('create', [
                    "alamat" => "Jalan Raya Tumpang RT 003, RW 005, Kec Talun",
                    "telp_rumah" => "(0342) 441306",
                    "telp_pertama" => "082126787808",
                    "telp_kedua" => "085815711117",
                    "email" => "yudhana.notaris@gmail.com",
                    "notaris_bersangkutan" => "NOTARIS/PPAT YUDHANA EKO PRASETYO, SH., M.Kn",
                    "ppat_bersangkutan" => "YUDHANA EKO PRASETYO, SH., M.Kn. Notaris - PPAT Kabupaten Blitar",
                    "besaran_nilai_tidak_kena_pajak" => 80000000,
                    "pengecekan" => 200000,
                    "surat_kuasa_membebankan_hak_tanggungan" => 250000,
                    "ploting_validasi" => 400000,
                    "harga_jual_materai" => 50000,
                    "harga_beli_materai" => 30000
                ])
            );
    }
}
