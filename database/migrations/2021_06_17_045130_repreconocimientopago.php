<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Repreconocimientopago extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('repreconocimientopago', function (Blueprint $table) {
            $table->increments('repid');
            $table->unsignedInteger('fecid');
            $table->unsignedInteger('sucid');
            $table->string('repconcepto');
            $table->string('reptipodocumento');
            $table->string('repnumerodocumento');
            $table->string('repfechadocumento');
            $table->string('repcategoria');
            $table->string('repimporte');
            $table->timestamps();

            $table->foreign('fecid')->references('fecid')->on('fecfechas');
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
        Schema::dropIfExists('repreconocimientopago');
    }
}
