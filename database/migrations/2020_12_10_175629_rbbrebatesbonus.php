<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Rbbrebatesbonus extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rbbrebatesbonus', function (Blueprint $table) {
            $table->increments('rbbid');
            $table->unsignedInteger('fecid');
            $table->unsignedInteger('usuid');
            $table->string('rbbporcentaje');
            $table->string('rbbcumplimiento');
            $table->timestamps();

            $table->foreign('fecid')->references('fecid')->on('fecfechas');
            $table->foreign('usuid')->references('usuid')->on('usuusuarios');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('rbbrebatesbonus');
    }
}
