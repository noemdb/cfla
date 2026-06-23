<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddStatusScheduleToAchievements extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('rols', function (Blueprint $table) {
            $table->boolean('status_schedule')->nullable()->default(true)->after('ffinal')->comment('Considerado para la Asistencia');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('rols', function (Blueprint $table) {
            $table->dropColumn('status_schedule');
        });
    }
}
