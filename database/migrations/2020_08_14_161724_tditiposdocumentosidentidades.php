<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Tditiposdocumentosidentidades extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tditiposdocumentosidentidades', function (Blueprint $table) {
            $table->increments('tdiid');
            $table->string('tdiabreviacion');
            $table->string('tdinombre');
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
        Schema::dropIfExists('tditiposdocumentosidentidades');
    }
}
