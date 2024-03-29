<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Catcategorias extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('catcategorias', function (Blueprint $table) {
            $table->increments('catid');
            $table->string('catnombre');
            $table->string('catimagenfondo');
            $table->string('caticono');
            $table->string('catcolorhover');
            $table->string('catcolor');
            $table->string('caticonoseleccionado');
            $table->string('caticonohover')->nullable();
            $table->string('catimagenfondoseleccionado')->nullable();
            $table->string('catimagenfondoopaco')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('catcategorias');
    }
}
