<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Fecfechas extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('fecfechas', function (Blueprint $table) {
            $table->increments('fecid');
            $table->string('fecfecha')->date();
            $table->string('fecdia');
            $table->string('fecmes');
            $table->string('fecano');
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
        Schema::dropIfExists('fecfechas');
    }
}
