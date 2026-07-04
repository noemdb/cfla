<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddExpectedLearningsToEifinalks extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('eifinalks', function (Blueprint $table) {
            $table->text('expected_learnings')->after('recommendations')->nullable()->comment('Estrategia');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('eifinalks', function (Blueprint $table) {
            $table->dropColumn('expected_learnings');
        });
    }
}
