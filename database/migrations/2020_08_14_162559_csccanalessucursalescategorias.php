<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Csccanalessucursalescategorias extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('csccanalessucursalescategorias', function (Blueprint $table) {
            $table->increments('cscid');
            $table->unsignedInteger('canid');
            $table->unsignedInteger('scaid');
            $table->unsignedInteger('fecid');
            $table->timestamps();

            $table->foreign('canid')->references('canid')->on('cancanales');
            $table->foreign('scaid')->references('scaid')->on('scasucursalescategorias');
            $table->foreign('fecid')->references('fecid')->on('fecfechas');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('csccanalessucursalescategorias');
    }
}
