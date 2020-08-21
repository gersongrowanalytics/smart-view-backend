<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Vsoventassso extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('vsoventassso', function (Blueprint $table) {
            $table->increments('vsoid');
            $table->unsignedInteger('fecid');
            $table->unsignedInteger('proid');
            // $table->unsignedInteger('cliid');
            $table->unsignedInteger('umeid');
            $table->unsignedInteger('tpmid');
            $table->string('vsocantidad');
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
        Schema::dropIfExists('vsoventassso');
    }
}
