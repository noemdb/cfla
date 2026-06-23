<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddStatusStandbyToCatchmentInterviews extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('catchment_interviews', function (Blueprint $table) {
            $table->boolean('status_standby')->default(false)->nullable()->after('observations')->default(false)->comment('Estado en espera?');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('catchment_interviews', function (Blueprint $table) {
            $table->dropColumn('status_standby');
        });
    }
}
