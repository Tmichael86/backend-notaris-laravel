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
        Schema::create('pengeluaran', function (Blueprint $table) {
            $table->id();
            $table->string('no_faktur');
            $table->integer('jenis_pengeluaran_id');
            $table->string('jumlah');
            $table->dateTime('tanggal_pengeluaran');
            $table->text('keterangan');
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
        Schema::dropIfExists('pengeluaran');
    }
};
