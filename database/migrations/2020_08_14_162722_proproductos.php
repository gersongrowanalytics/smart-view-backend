<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Proproductos extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('proproductos', function (Blueprint $table) {
            $table->increments('proid');
            $table->unsignedInteger('catid')->nullable();
            $table->string('prosku');
            $table->string('pronombre');
            $table->string('proimagen');
            $table->timestamps();

            $table->foreign('catid')->references('catid')->on('catcategorias');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('proproductos');
    }
}
