<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Tprtipospromociones extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tprtipospromociones', function (Blueprint $table) {
            $table->increments('tprid');
            $table->string('tprnombre');
            $table->string('tpricono')->nullable();
            $table->string('tprcolor')->nullable();
            $table->string('tprcolorbarra')->nullable();
            $table->string('tprcolortooltip')->nullable();
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
        Schema::dropIfExists('tprtipospromociones');
    }
}
