<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDateStartCensusToLapsosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('lapsos', function (Blueprint $table) {
            $table->date('date_start_census')->nullable()->after('date_cutnote')->comment('Fecha de inicio del censo');
            $table->time('time_start_census')->nullable()->after('date_end_census')->comment('Hora de inicio');
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
            $table->dropColumn('date_start_census');
            $table->dropColumn('time_start_census');
        });
    }
}
