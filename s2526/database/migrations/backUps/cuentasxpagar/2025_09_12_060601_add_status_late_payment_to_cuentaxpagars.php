<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddStatusLatePaymentToCuentaxpagars extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('cuentaxpagars', function (Blueprint $table) {
            $table->boolean('status_late_payment')->nullable()->default(false)->after('status_bad')->comment('Cuota por morosidad');
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
            $table->dropColumn('status_late_payment');
        });
    }
}
