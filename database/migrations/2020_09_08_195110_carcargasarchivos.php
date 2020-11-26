<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Carcargasarchivos extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('carcargasarchivos', function (Blueprint $table) {
            $table->increments('carid');
            $table->unsignedInteger('tcaid');
            $table->unsignedInteger('fecid')->nullable();
            $table->unsignedInteger('usuid');
            $table->string('carnombrearchivo');
            $table->string('carubicacion');
            $table->string('carurl')->nullable();
            $table->boolean('carexito');
            $table->timestamps();

            $table->foreign('tcaid')->references('tcaid')->on('tcatiposcargasarchivos');
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
        Schema::dropIfExists('carcargasarchivos');
    }
}
