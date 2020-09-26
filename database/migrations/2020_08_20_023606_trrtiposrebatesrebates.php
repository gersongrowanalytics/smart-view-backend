<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Trrtiposrebatesrebates extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('trrtiposrebatesrebates', function (Blueprint $table) {
            $table->increments('trrid');
            $table->unsignedInteger('treid');
            $table->unsignedInteger('rtpid');
            $table->timestamps();

            $table->foreign('treid')->references('treid')->on('tretiposrebates');
            $table->foreign('rtpid')->references('rtpid')->on('rtprebatetipospromociones');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('trrtiposrebatesrebates');
    }
}
