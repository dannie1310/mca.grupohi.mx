<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTirosConceptosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tiros_conceptos', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('id_tiro')->unsigned();
            $table->integer('id_concepto');
            $table->timestamp('inicio_vigencia');
            $table->timestamp('fin_vigencia');
            $table->integer('registro');
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
        Schema::drop('tiros_conceptos');
    }
}
