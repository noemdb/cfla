<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDatePreclosingToLapsosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('lapsos', function (Blueprint $table) {
            $table->time('time_preclosing')->nullable()->after('time_end_census')->comment('Hora de cierre');
            $table->date('date_preclosing')->nullable()->after('time_end_census')->comment('Fecha de cierre');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('lapsos', function (Blueprint $table) {
            $table->dropColumn('time_preclosing');
            $table->dropColumn('date_preclosing');
        });
    }
}
