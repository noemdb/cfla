<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddStatusNoteReportToPevaluacions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pevaluacions', function (Blueprint $table) {
            $table->boolean('status_note_report')->nullable()->default(true)->after('status_official')->comment('En Informe de Notas');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('pevaluacions', function (Blueprint $table) {
            $table->dropColumn('status_note_report');
        });
    }
}
