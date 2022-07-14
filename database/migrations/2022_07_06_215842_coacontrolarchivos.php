<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Coacontrolarchivos extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('coacontrolarchivos', function (Blueprint $table) {
            $table->increments('coaid');
            $table->unsignedInteger('usuid');
            $table->unsignedInteger('carid');
            $table->unsignedInteger('fecid')->nullable();
            $table->unsignedInteger('estid');
            $table->unsignedInteger('badid');
            $table->integer('coadiasretraso')->nullable();
            $table->string('coafechasubida')->nullable();
            $table->date('coafechacaducidad')->nullable();
            $table->timestamps();

            $table->foreign('fecid')->references('fecid')->on('fecfechas');
            $table->foreign('usuid')->references('usuid')->on('usuusuarios');
            $table->foreign('carid')->references('carid')->on('carcargasarchivos');
            $table->foreign('estid')->references('estid')->on('estestados');
            $table->foreign('badid')->references('badid')->on('badbasedatos');
            
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('coacontrolarchivos');
    }
}
