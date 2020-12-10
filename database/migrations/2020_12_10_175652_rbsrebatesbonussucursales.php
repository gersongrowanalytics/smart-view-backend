<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Rbsrebatesbonussucursales extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rbsrebatesbonussucursales', function (Blueprint $table) {
            $table->increments('rbsid');
            $table->unsignedInteger('fecid');
            $table->unsignedInteger('rbbid');
            $table->unsignedInteger('sucid');
            $table->string('rbsobjetivo');
            $table->string('rbsreal');
            $table->string('rbscumplimiento');
            $table->string('rbsrebate');
            $table->timestamps();

            $table->foreign('fecid')->references('fecid')->on('fecfechas');
            $table->foreign('rbbid')->references('rbbid')->on('rbbrebatesbonus');
            $table->foreign('sucid')->references('sucid')->on('sucsucursales');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('rbsrebatesbonussucursales');
    }
}
