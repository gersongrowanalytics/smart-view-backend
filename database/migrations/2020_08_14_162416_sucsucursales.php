<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Sucsucursales extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sucsucursales', function (Blueprint $table) {
            $table->increments('sucid');
            $table->unsignedInteger('treid')->nullable();
            $table->unsignedInteger('zonid')->nullable();
            $table->string('sucnombre');
            $table->timestamps();

            $table->foreign('treid')->references('treid')->on('tretiposrebates');
            $table->foreign('zonid')->references('zonid')->on('zonzonas');
            
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sucsucursales');
    }
}
