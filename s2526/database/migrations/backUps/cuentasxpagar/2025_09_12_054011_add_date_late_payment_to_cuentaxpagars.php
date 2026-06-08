<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDateLatePaymentToCuentaxpagars extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('cuentaxpagars', function (Blueprint $table) {
            $table->date('date_late_payment')->nullable()->after('date_expiration')->comment('Fecha de recargo por morosidad');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('cuentaxpagars', function (Blueprint $table) {
            $table->dropColumn('date_late_payment');
        });
    }
}
