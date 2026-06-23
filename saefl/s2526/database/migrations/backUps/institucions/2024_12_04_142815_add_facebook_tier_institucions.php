<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFacebookTierInstitucions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('institucions', function (Blueprint $table) {
            $table->text('facebook_tier',32)->nullable()->after('facebook_access_token');
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
            $table->dropColumn('facebook_tier');
        });
    }
}
