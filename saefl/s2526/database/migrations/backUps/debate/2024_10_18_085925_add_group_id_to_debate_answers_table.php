<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddGroupIdToDebateAnswersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('debate_answers', function (Blueprint $table) {
            $table->unsignedInteger('group_id')->after('seccion_id')->nullable()->comment('Grupo');
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
            $table->dropColumn('group_id');
        });
    }
}
