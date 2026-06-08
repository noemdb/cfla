<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddEstrategiasToEiprojectreviews extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('eiprojectreviews', function (Blueprint $table) {
            $table->string('estrategias')->after('quienes_nos_pueden_apoyar')->nullable()->comment('Estrategia');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('eiprojectreviews', function (Blueprint $table) {
            $table->dropColumn('estrategias');
        });
    }
}
