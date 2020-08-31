<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Prppromocionesproductos extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('prppromocionesproductos', function (Blueprint $table) {
            $table->increments('prpid');
            $table->unsignedInteger('prmid');
            $table->unsignedInteger('proid');
            $table->integer('prpcantidad');
            $table->timestamps();

            $table->foreign('prmid')->references('prmid')->on('prmpromociones');
            $table->foreign('proid')->references('proid')->on('proproductos');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('prppromocionesproductos');
    }
}
