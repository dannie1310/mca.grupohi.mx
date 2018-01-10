<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddForeignkeyEstimacionConciliacionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('estimacion_conciliacion', function (Blueprint $table) {
            $table->integer('id_conciliacion')->unsigned()->change();
            $table->foreign('id_conciliacion')->references('idconciliacion')->on('conciliacion');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('estimacion_conciliacion', function (Blueprint $table) {
            $table->dropForeign('estimacion_conciliacion_id_conciliacion_foreign');
        });
    }
}
