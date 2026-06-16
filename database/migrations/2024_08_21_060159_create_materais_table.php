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
        Schema::create('materais', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->unsignedInteger('materai_masuk');
            $table->unsignedInteger('materai_keluar');
            $table->unsignedInteger('stok_materai');
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
        Schema::dropIfExists('materais');
    }
};
