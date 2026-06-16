<?php

namespace Database\Seeders;

use App\Http\Libraries\System;
use App\Models\JenisPengeluaran;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class JenisPengeluaranSeeder extends Seeder
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
                "nama" => "ATK"
            ],
            [
                "nama" => "Materai 10 Ribu"
            ],
            [
                "nama" => "Voucher Pendirian"
            ],
            [
                "nama" => "Voucher Perubahan"
            ],
            [
                "nama" => "Tinta Printer"
            ],
            [
                "nama" => "Makanan dan Minuman"
            ],
            [
                "nama" => "Surat Perintah Setor"
            ]
        ];

        // each jenis pengeluaran
        foreach ($data as $item) {
            JenisPengeluaran::create(
                System::crudIdentity('create', $item)
            );
        }
    }
}
