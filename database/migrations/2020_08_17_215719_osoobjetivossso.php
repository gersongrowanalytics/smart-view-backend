<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Osoobjetivossso extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('osoobjetivossso', function (Blueprint $table) {
            $table->increments('osoid');
            $table->unsignedInteger('fecid');
            $table->unsignedInteger('proid');
            $table->unsignedInteger('sucid');
            $table->unsignedInteger('umeid');
            $table->unsignedInteger('tpmid');
            $table->string('osocantidad');
            $table->string('osovalorizado');
            $table->timestamps();

            $table->foreign('fecid')->references('fecid')->on('fecfechas');
            $table->foreign('proid')->references('proid')->on('proproductos');
            $table->foreign('sucid')->references('sucid')->on('sucsucursales');
            $table->foreign('umeid')->references('umeid')->on('umeunidadesmedidas');
            $table->foreign('tpmid')->references('tpmid')->on('tpmtiposmonedas');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('osoobjetivossso');
    }
}
