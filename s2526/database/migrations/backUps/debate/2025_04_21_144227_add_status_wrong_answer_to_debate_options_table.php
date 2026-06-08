<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddStatusWrongAnswerToDebateOptionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('debate_options', function (Blueprint $table) {
            $table->boolean('status_wrong_answer')->default(false)->after('status_option_correct');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('debate_options', function (Blueprint $table) {
            $table->dropColumn('status_wrong_answer');
        });
    }
}
