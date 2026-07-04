<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddEnabledForAdministrativeToPlanpagosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('planpagos', function (Blueprint $table) {
            $table->enum('enabled_for_administrative',['true','false'])->after('status_active')->default('true')->comment('Visualizar en Insc. Administrativas');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('planpagos', function (Blueprint $table) {
            $table->dropColumn('enabled_for_administrative');
        });
    }
}
