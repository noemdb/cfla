<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DelAssitScheduleIdToAssitDaysTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('assit_days', function (Blueprint $table) {
            $table->dropColumn('assit_schedule_id');
            $table->dropForeign('assit_schedule_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('assit_days', function (Blueprint $table) {
            $table->unsignedBigInteger('assit_schedule_id');
            $table->foreign('assit_schedule_id')->references('id')->on('assit_schedules')->onDelete('cascade')->onUpdate('cascade');
        });
    }
}
