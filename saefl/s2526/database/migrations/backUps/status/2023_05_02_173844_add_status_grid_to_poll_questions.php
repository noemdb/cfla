<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddStatusGridToPollQuestions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('poll_questions', function (Blueprint $table) {
            $table->enum('status_grid',['true','false'])->after('body')->default('false')->comment('Opciones en modo grip');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('poll_questions', function (Blueprint $table) {
            $table->dropColumn('status_grid');
        });
    }
}
