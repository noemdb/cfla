<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddStatusCancelPaymentToPlanpagos extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('planpagos', function (Blueprint $table) {
            $table->enum('status_cancel',['true','false'])->after('status_active')->default('false')->comment('Permitir la anulación de pagos en fechas posteriores');
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
            $table->dropColumn('status_cancel');
        });
    }
}
