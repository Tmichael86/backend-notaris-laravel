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
        Schema::create('konfigurasi_umum', function (Blueprint $table) {
            $table->id();

            // => Konfigurasi Alamat Dan Data Notaris / PPAT
            $table->string('alamat')->nullable();
            $table->string('telp_rumah', 13)->nullable();
            $table->string('telp_pertama', 13)->nullable();
            $table->string('telp_kedua', 13)->nullable();
            $table->string('email')->nullable();
            $table->string("notaris_bersangkutan")->nullable();
            $table->string("ppat_bersangkutan")->nullable();
            // Konfigurasi Pajak

            // => Pajak APHB
            $table->unsignedBigInteger(column: 'besaran_nilai_tidak_kena_pajak')->nullable();

            // => Pajak APTH
            $table->unsignedBigInteger('pengecekan')->nullable();
            $table->unsignedBigInteger('surat_kuasa_membebankan_hak_tanggungan')->nullable();
            $table->unsignedBigInteger('ploting_validasi')->nullable();
            
            $table->unsignedBigInteger('harga_beli_materai')->nullable();
            $table->unsignedBigInteger('harga_jual_materai')->nullable();

            // SOP
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
        Schema::dropIfExists('konfigurasi_umum');
    }
};
