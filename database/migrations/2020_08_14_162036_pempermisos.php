<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Pempermisos extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pempermisos', function (Blueprint $table) {
            $table->increments('pemid');
            $table->unsignedInteger('tpemid');
            $table->string('pemnombre');
            $table->string('pemslug');
            $table->string('pemruta')->nullable();
            $table->timestamps();

            $table->foreign('tpemid')->references('tpemid')->on('tpemtipospermisos');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pempermisos');
    }
}
