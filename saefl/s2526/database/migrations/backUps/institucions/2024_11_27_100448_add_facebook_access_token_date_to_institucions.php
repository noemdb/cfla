<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFacebookAccessTokenDateToInstitucions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('institucions', function (Blueprint $table) {
            $table->datetime('facebook_access_token_date')->nullable()->after('date_suspend');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('institucions', function (Blueprint $table) {
            $table->dropColumn('facebook_access_token_date');
        });
    }
}
