<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Perpersonas extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('perpersonas', function (Blueprint $table) {
            $table->increments('perid');
            $table->unsignedInteger('tdiid');
            $table->string('pernumerodocumentoidentidad');
            $table->string('pernombrecompleto');
            $table->string('pernombre');
            $table->string('perapellidopaterno');
            $table->string('perapellidomaterno');
            $table->timestamps();

            $table->foreign('tdiid')->references('tdiid')->on('tditiposdocumentosidentidades');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('perpersonas');
    }
}
