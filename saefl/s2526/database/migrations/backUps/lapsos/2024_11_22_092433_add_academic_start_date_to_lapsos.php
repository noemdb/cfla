<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAcademicStartDateToLapsos extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('lapsos', function (Blueprint $table) {
            $table->date('academic_start_date')->nullable()->after('ffinal')->comment('Fecha de inicio de actividades académicas');
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
            $table->dropColumn('academic_start_date');
        });
    }
}
