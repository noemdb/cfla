<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPensumIdToDebateQuestionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('debate_questions', function (Blueprint $table) {
            $table->unsignedInteger('pensum_id')->after('debate_id')->nullable()->comment('Área de formación');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('debate_questions', function (Blueprint $table) {
            $table->dropColumn('pensum_id');
        });
    }
}
