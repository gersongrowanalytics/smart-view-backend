<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Cspcanalessucursalespromociones extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cspcanalessucursalespromociones', function (Blueprint $table) {
            $table->increments('cspid');
            $table->unsignedInteger('cscid');
            $table->unsignedInteger('fecid');
            $table->unsignedInteger('prmid');
            $table->string('cspvalorizado');
            $table->string('cspplanchas');
            $table->boolean('cspcompletado')->default(false);
            $table->timestamps();

            $table->foreign('cscid')->references('cscid')->on('csccanalessucursalescategorias');
            $table->foreign('fecid')->references('fecid')->on('fecfechas');
            $table->foreign('prmid')->references('prmid')->on('prmpromociones');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cspcanalessucursalespromociones');
    }
}
