<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddQuotaIdToConceptoPagos extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('concepto_pagos', function (Blueprint $table) {
            $table->unsignedInteger('quota_id')->after('cuentaxpagar_id')->nullable()->comment('Cuota de morosidad');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('concepto_pagos', function (Blueprint $table) {
            $table->dropColumn('quota_id');
        });
    }
}
