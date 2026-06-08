<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIngresoIdToConceptoCanceladosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('concepto_cancelados', function (Blueprint $table) {
            $table->unsignedInteger('ingreso_id')->after('exchange_ammount')->nullable()->comment('ingreso');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('concepto_cancelados', function (Blueprint $table) {
            $table->dropColumn('ingreso_id');
        });
    }
}
