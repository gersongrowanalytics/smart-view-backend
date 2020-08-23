<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Scasucursalescategorias extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('scasucursalescategorias', function (Blueprint $table) {
            $table->increments('scaid');
            $table->unsignedInteger('sucid');
            $table->unsignedInteger('catid');
            $table->unsignedInteger('fecid');
            $table->unsignedInteger('tsuid')->nullable();
            $table->string('scavalorizadoobjetivo');
            $table->string('scavalorizadoreal');
            $table->string('scavalorizadotogo');
            $table->timestamps();

            $table->foreign('tsuid')->references('tsuid')->on('tsutipospromocionessucursales');
            $table->foreign('sucid')->references('sucid')->on('sucsucursales');
            $table->foreign('catid')->references('catid')->on('catcategorias');
            $table->foreign('fecid')->references('fecid')->on('fecfechas');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('scasucursalescategorias');
    }
}
