<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Vsiventasssi extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('vsiventasssi', function (Blueprint $table) {
            $table->increments('vsiid');
            $table->unsignedInteger('fecid');
            $table->unsignedInteger('proid');
            // $table->unsignedInteger('cliid');
            $table->unsignedInteger('umeid');
            $table->unsignedInteger('tpmid');
            $table->string('vsicantidad');
            $table->timestamps();

            $table->foreign('fecid')->references('fecid')->on('fecfechas');
            $table->foreign('proid')->references('proid')->on('proproductos');
            // $table->foreign('cliid')->references('cliid')->on('cliclientes');
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
        Schema::dropIfExists('vsiventasssi');
    }
}
