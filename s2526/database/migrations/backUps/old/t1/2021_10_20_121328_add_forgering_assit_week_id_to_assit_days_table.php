<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForgeringAssitWeekIdToAssitDaysTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('assit_days', function (Blueprint $table) {
            $table->foreign('assit_week_id')->references('id')->on('assit_weeks')->onDelete('cascade')->onUpdate('cascade');
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
            $table->dropForeign('assit_week_id');
        });
    }
}
