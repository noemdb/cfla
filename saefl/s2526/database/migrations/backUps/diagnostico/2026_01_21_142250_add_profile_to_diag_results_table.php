<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddProfileToDiagResultsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('diag_results', function (Blueprint $table) {
            $table->longText('profile')->nullable()->after('precision')->comment('PERFIL DIAGNÓSTICO INICIAL');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('diag_results', function (Blueprint $table) {
            $table->dropColumn('profile');
        });
    }
}
