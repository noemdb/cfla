<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddEnableLatePaymentToCuentaxpagars extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('cuentaxpagars', function (Blueprint $table) {
            $table->boolean('enable_late_payment')->nullable()->default(false)->after('status_bad')->comment('Habilitada para cargo por morosidad');
        });
    }

    /**enable_late_payment
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('cuentaxpagars', function (Blueprint $table) {
            $table->dropColumn('enable_late_payment');
        });
    }
}
