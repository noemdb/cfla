<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDiagMainIdToDiagQuestionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('diag_questions', function (Blueprint $table) {
            $table->foreignId('diag_main_id')->nullable()->constrained('diag_mains')->onDelete('cascade')->after('pensum_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('diag_questions', function (Blueprint $table) {
            $table->dropForeign(['diag_main_id']);
            $table->dropColumn('diag_main_id');
        });
    }
}
