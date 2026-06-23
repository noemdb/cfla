<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddUserIdToDebateOptionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('debate_options', function (Blueprint $table) {
            $table->unsignedInteger('user_id')->default(11)->after('question_id')->nullable()->comment('Última revisión');
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
            $table->dropColumn('user_id');
        });
    }
}
