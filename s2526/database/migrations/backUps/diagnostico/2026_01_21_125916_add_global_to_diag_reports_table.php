<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddGlobalToDiagReportsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('diag_reports', function (Blueprint $table) {
            $table->longText('global')->nullable()->comment('RESULTADOS GLOBALES')->after('status');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('diag_reports', function (Blueprint $table) {
            $table->dropColumn('global');
        });
    }
}
