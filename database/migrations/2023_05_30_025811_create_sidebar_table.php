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
        Schema::create('sidebar', function (Blueprint $table) {
            $table->id();
            $table->integer('sidebar_parent_id')->nullable()->default(0);
            $table->string('sidebar_nama');
            $table->string('sidebar_route');
            $table->string('sidebar_kode');
            $table->string('sidebar_icon');
            $table->mediumInteger('sidebar_index');
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
        Schema::dropIfExists('sidebar');
    }
};
