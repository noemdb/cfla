<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDateEndCensusToLapsosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('lapsos', function (Blueprint $table) {
            $table->date('date_end_census')->nullable()->after('date_cutnote')->comment('Fecha de finalización del censo');
            $table->time('time_end_census')->nullable()->after('date_end_census')->comment('Hora de finalización');
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
            $table->dropColumn('date_end_census');
            $table->dropColumn('time_end_census');
        });
    }
}
