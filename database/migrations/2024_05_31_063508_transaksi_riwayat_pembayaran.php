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
        Schema::create('transaksi_riwayat_pembayaran', function (Blueprint $table) {
            $table->id();
            $table->date('tanggal_pembayaran');
            $table->string('no_transaksi')->nullable();
            $table->unsignedBigInteger('jumlah_dibayar');
            $table->integer('pembayaran_ke');
            $table->text('keterangan')->nullable();
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
        Schema::dropIfExists('transaksi_riwayat_pembayaran');
    }
};
