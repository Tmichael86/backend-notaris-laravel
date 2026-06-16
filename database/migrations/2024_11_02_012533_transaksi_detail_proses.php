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
        Schema::create('transaksi_detail_proses', function(Blueprint $table) {
            $table->id();
            $table->foreignId('transaksi_id');
            $table->foreignId('jenis_pekerjaan_id');
            $table->foreignId('prosesId'); // pekerjaan proses id

            $table->string('pekerjaanNama');
            $table->string('kategoriNama');
            $table->string('prosesNama');
            $table->json('atribut')->nullable();
            $table->text('catatan')->nullable();
            $table->smallInteger('isValidate')->default(0);

            $table->integer('created_by')->nullable();
            $table->integer('updated_by')->nullable();
            $table->smallInteger('status');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('transaksi_detail_proses');
    }
};
