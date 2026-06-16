<?php

namespace Database\Seeders;

use App\Http\Libraries\System;
use App\Models\KategoriPekerjaan;
use Illuminate\Database\Seeder;

class KategoriPekerjaanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data = [
            [
                "nama" => "UMK"
            ],
            [
                "nama" => "Non UMK"
            ],
            [
                "nama" => "-"
            ],
            [
                "nama" => "Jual Beli"
            ],
            [
                "nama" => "Waris"
            ],
            [
                "nama" => "Hibah"
            ],
            [
                "nama" => "Tukar Menukar"
            ],
            [
                "nama" => "2 bidang"
            ],
            [
                "nama" => "3 bidang"
            ],
            [
                "nama" => "4 bidang"
            ],
            [
                "nama" => "5 bidang"
            ],
            [
                "nama" => "Luas"
            ],
            [
                "nama" => "Nama dan Tanggal Lahir"
            ],
            [
                "nama" => "Bidang Ketukar\/Surat Ukur Ketukar"
            ],
            [
                "nama" => "Khusus"
            ],
            [
                "nama" => "Biasa"
            ],
            [
                "nama" => "Wakaf"
            ],
            [
                "nama" => "Peningkatan Hak dr HGB ke HM"
            ],
            [
                "nama" => "Penurunan Hak (inbreng)"
            ],
            [
                "nama" => "Penghapusan Hak"
            ],
            [
                "nama" => "PMA"
            ],
            [
                "nama" => "PMDN"
            ]
        ];

        // -- each kategori pekerjaan
        foreach ($data as $item) {
            KategoriPekerjaan::create(
                System::crudIdentity('create', $item)
            );
        }
    }
}
