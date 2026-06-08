<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCompetitionIdToDebateAnswersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('debate_answers', function (Blueprint $table) {
            $table->unsignedInteger('competition_id')->after('seccion_id')->nullable()->comment('Competición');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('debate_answers', function (Blueprint $table) {
            $table->dropColumn('competition_id');
        });
    }
}
