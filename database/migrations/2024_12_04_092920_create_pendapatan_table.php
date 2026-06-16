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
        Schema::create("pendapatan", function (Blueprint $table) {
            $table->id();
            $table->integer('jenis_pembayaran_id')->nullable();
            $table->date("tanggal");
            $table->integer("penghasilan")->default(0);
            $table->integer("pengeluaran")->default(0);
            $table->integer("pendapatan")->default(0);
            $table->integer("saldo")->default(0);
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
        Schema::dropIfExists('pendapatan');
    }
};
