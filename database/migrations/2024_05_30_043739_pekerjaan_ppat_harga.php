<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pekerjaan_ppat_harga', function (Blueprint $table) {
            $table->id();
            $table->integer('pekerjaan_ppat_id');
            $table->string('harga');
            $table->integer('kategori_pekerjaan_id');
            $table->string('estimasi_waktu');
            $table->integer('created_by')->nullable();
            $table->integer('updated_by')->nullable();
            $table->timestamps();
            $table->smallInteger('status');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pekerjaan_ppat_harga');
    }
};
