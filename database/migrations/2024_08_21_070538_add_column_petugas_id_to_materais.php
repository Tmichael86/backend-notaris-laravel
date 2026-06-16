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
        Schema::table('materais', function (Blueprint $table) {
            $table->integer('petugas_id');
            $table->integer('is_transaksi')->nullable();
            $table->integer('is_add_materai')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('materais', function (Blueprint $table) {
            $table->dropColumn('petugas_id');
        });
    }
};
