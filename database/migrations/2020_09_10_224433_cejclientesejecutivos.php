<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Cejclientesejecutivos extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cejclientesejecutivos', function (Blueprint $table) {
            $table->increments('cejid');
            $table->unsignedInteger('cejejecutivo');
            $table->unsignedInteger('cejcliente');
            $table->timestamps();

            $table->foreign('cejejecutivo')->references('usuid')->on('usuusuarios');
            $table->foreign('cejcliente')->references('usuid')->on('usuusuarios');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
