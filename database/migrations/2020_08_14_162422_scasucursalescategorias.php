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
            $table->string('scavalorizadoobjetivo')->nullable();
            $table->string('scavalorizadoreal')->nullable();
            $table->string('scavalorizadotogo')->nullable();
            $table->string('scavalorizadorebate')->nullable();
            $table->string('scaporcentajecumplimiento')->nullable();
            $table->string('scaiconocategoria')->nullable();

            $table->string('scaobjetivotrimestral')->nullable();
            $table->string('scarealtrimestral')->nullable();
            $table->string('scafacturartrimestral')->nullable();
            $table->string('scacumplimientotrimestral')->nullable();
            $table->string('scarebatetrimestral')->nullable();
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
