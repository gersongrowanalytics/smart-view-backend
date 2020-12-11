<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Trftrimestresfechas extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('trftrimestresfechas', function (Blueprint $table) {
            $table->increments('trfid');
            $table->unsignedInteger('triid');
            $table->unsignedInteger('fecid');
            $table->timestamps();

            $table->foreign('fecid')->references('fecid')->on('fecfechas');
            $table->foreign('triid')->references('triid')->on('tritrimestres');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('trftrimestresfechas');
    }
}
