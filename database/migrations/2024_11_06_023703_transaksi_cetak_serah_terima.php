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
        Schema::create('transaksi_cetak_serah_terima', function (Blueprint $table) {
            $table->id();
            $table->string('no_transaksi');
            $table->string("list_keperluan")->nullable(true);
            $table->string("uraian")->nullable(true);
            $table->string("keperluan")->nullable(true);

            $table->integer('created_by');
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
        Schema::dropIfExists('transaksi_cetak_serah_terima');
    }
};
