<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddLeaderIdToAreaConocimientos extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('area_conocimientos', function (Blueprint $table) {
            $table->unsignedInteger('leader_id')->after('pestudio_id')->nullable()->comment('Jefe del Área');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('area_conocimientos', function (Blueprint $table) {
            $table->dropColumn('leader_id');
        });
    }
}
