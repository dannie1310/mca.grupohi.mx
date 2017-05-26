<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddRfcToSindicatos extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sindicatos', function (Blueprint $table) {
           $table->string('rfc');
           $table->integer('usuario_registro');
           $table->integer('usuario_desactivo')->nullable();
           $table->text('motivo')->nullable();
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
        Schema::table('sindicatos', function (Blueprint $table) {
            $table->dropColumn(['rfc','usuario_registro', 'usuario_desactivo', 'motivo']);
            $table->dropTimestamps();
        });
    }
}
