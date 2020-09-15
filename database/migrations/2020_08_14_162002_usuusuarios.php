<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Usuusuarios extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('usuusuarios', function (Blueprint $table) {
            $table->increments('usuid');
            $table->unsignedInteger('tpuid');
            $table->unsignedInteger('perid');
            $table->unsignedInteger('estid')->default(2);
            $table->string('ususoldto')->nullable();
            $table->string('usuusuario')->nullable();
            $table->string('usucorreo')->nullable();
            $table->string('usucontrasena')->nullable();
            $table->string('usutoken');
            $table->timestamps();

            $table->foreign('tpuid')->references('tpuid')->on('tputiposusuarios');
            $table->foreign('perid')->references('perid')->on('perpersonas');
            $table->foreign('estid')->references('estid')->on('estestados');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('usuusuarios');
    }
}
