<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Rscrbsscategorias extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rscrbsscategorias', function (Blueprint $table) {
            $table->increments('rscid');
            $table->unsignedInteger('fecid');
            $table->unsignedInteger('rbbid');
            $table->unsignedInteger('sucid');
            $table->unsignedInteger('rbsid');
            $table->unsignedInteger('catid');
            $table->string('rscestado');
            $table->timestamps();

            $table->foreign('fecid')->references('fecid')->on('fecfechas');
            $table->foreign('rbbid')->references('rbbid')->on('rbbrebatesbonus');
            $table->foreign('sucid')->references('sucid')->on('sucsucursales');
            $table->foreign('rbsid')->references('rbsid')->on('rbsrebatesbonussucursales');
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
        Schema::dropIfExists('rscrbsscategorias');
    }
}
