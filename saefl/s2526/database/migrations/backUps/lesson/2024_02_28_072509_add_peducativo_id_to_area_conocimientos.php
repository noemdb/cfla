<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPeducativoIdToAreaConocimientos extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('area_conocimientos', function (Blueprint $table) {
            $table->smallInteger('peducativo_id')->unsigned()->nullable()->after('id')->comment('Plan Educativo');
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
            $table->dropColumn(['peducativo_id']);
        });
    }
}
