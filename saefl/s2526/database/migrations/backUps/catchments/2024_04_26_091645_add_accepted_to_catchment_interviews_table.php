<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAcceptedToCatchmentInterviewsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('catchment_interviews', function (Blueprint $table) {
            $table->boolean('accepted')->nullable()->after('justification_for_not_participating_in_catholic_activities')->default(false)->comment('¿Aceptado?');
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
            $table->dropColumn('accepted');
        });
    }
}
