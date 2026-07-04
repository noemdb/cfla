<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddShowQuantitativeIndicatorsToPeducativos extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('peducativos', function (Blueprint $table) {
            $table->enum('show_quantitative_indicators',['true','false'])->after('status_active')->default('true')->comment('Indique si se muestran o no los indicadores cuantitativos');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('peducativos', function (Blueprint $table) {
            $table->dropColumn('show_quantitative_indicators');
        });
    }
}
