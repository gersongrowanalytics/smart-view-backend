<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Prbpromocionesbonificaciones extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('prbpromocionesbonificaciones', function (Blueprint $table) {
            $table->increments('prbid');
            $table->unsignedInteger('prmid');
            $table->unsignedInteger('proid');
            $table->integer('prbcantidad');
            $table->string('prbproductoppt')->nullable();
            $table->string('prbcomprappt');
            $table->string('prbcodigoprincipal');
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
        Schema::dropIfExists('prbpromocionesbonificaciones');
    }
}
