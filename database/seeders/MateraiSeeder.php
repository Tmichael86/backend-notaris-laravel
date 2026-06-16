<?php

namespace Database\Seeders;

use App\Http\Libraries\System;
use App\Models\Materai;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class MateraiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Materai::query()
            ->create(
                System::crudIdentity('create', [
                    'date' => Carbon::now(),
                    'keterangan' => '-',
                    'materai_masuk' => 50,
                    'materai_keluar' => 0,
                    'stok_materai' => 50,
                    'petugas_id' => 1,
                ])
            );
    }
}
