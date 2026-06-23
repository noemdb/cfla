<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDiagMainIdToDiagSessionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('diag_sessions', function (Blueprint $table) {
            $table->foreignId('diag_main_id')->nullable()->constrained('diag_mains')->onDelete('set null')->after('pensum_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('diag_sessions', function (Blueprint $table) {
            $table->dropForeign(['diag_main_id']);
            $table->dropColumn('diag_main_id');
        });
    }
}
