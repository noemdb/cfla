<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddEstrategiasToEiprojectsummaries extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('eiprojectsummaries', function (Blueprint $table) {
            $table->string('estrategias')->after('enfasis_curriculares')->nullable()->comment('Estrategia');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('eiprojectsummaries', function (Blueprint $table) {
            $table->dropColumn('estrategias');
        });
    }
}
