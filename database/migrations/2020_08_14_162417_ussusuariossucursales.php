<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Ussusuariossucursales extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ussusuariossucursales', function (Blueprint $table) {
            $table->increments('ussid');
            $table->unsignedInteger('usuid');
            $table->unsignedInteger('sucid');
            $table->timestamps();

            $table->foreign('usuid')->references('usuid')->on('usuusuarios');
            $table->foreign('sucid')->references('sucid')->on('sucsucursales');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ussusuariossucursales');
    }
}
