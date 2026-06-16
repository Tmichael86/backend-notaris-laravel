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
        Schema::create('sidebar_akses', function (Blueprint $table) {
            $table->id();
            $table->integer('sidebar_id');
            $table->integer('group_id');
            $table->smallInteger('read');
            $table->smallInteger('create');
            $table->smallInteger('update');
            $table->smallInteger('delete');
            $table->integer('created_by')->nullable();
            $table->integer('updated_by')->nullable();
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
        Schema::dropIfExists('sidebar_akses');
    }
};
