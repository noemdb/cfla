<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTokenToCatchmentInterviewsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('catchment_interviews', function (Blueprint $table) {
            $table->text('token')->after('catchment_id')->nullable()->comment('Token');
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
            $table->dropColumn('token');
        });
    }
}
