<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddContextToDebateQuestionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('debate_questions', function (Blueprint $table) {
            $table->text('context')->nullable()->after('attachment')->comment('Contexto');
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
            $table->dropColumn('context');
        });
    }
}
