<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAssitWeekIdToAssitDaysTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('assit_days', function (Blueprint $table) {
            $table->unsignedBigInteger('assit_week_id')->after('id')->nullable();

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
            $table->dropColumn('assit_week_id');

        });
    }
}
