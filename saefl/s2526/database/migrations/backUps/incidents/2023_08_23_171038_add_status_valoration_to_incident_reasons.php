<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddStatusValorationToIncidentReasons extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('incident_reasons', function (Blueprint $table) {
            $table->boolean('status_valoration')->nullable()->default(true)->after('name')->default(false)->comment('Valoración (Positiva/Negativa)');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('incident_reasons', function (Blueprint $table) {
            $table->dropColumn('status_valoration');
        });
    }
}
