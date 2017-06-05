<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddIdmotivosToViajesnetos extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('viajesnetos', function (Blueprint $table) {
            $table->integer('IdMotivo')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('viajesnetos', function (Blueprint $table) {
            $table->dropColumn(['IdMotivo']);
        });
    }
}
