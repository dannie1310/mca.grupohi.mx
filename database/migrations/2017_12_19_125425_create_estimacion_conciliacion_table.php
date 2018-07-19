<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEstimacionConciliacionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('estimacion_conciliacion', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('id_conciliacion');
            $table->integer('id_estimacion');
            $table->timestamp('fechaHoraRegistro');
            $table->integer('registro');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('estimacion_conciliacion');
    }
}
