<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDayOfWeekToEiplanningwstrategiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('eiplanningwstrategies', function (Blueprint $table) {
            $table->string('day_of_week')->nullable()->after('eiplanningwk_id');
            $table->index(['eiplanningwk_id', 'day_of_week']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('eiplanningwstrategies', function (Blueprint $table) {
            $table->dropIndex(['eiplanningwk_id', 'day_of_week']);
            $table->dropColumn('day_of_week');
        });
    }
}
