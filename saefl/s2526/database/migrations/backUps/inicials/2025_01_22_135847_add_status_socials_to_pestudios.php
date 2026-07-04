<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddStatusSocialsToPestudios extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pestudios', function (Blueprint $table) {
            $table->boolean('status_socials')->nullable()->default(true)->after('status_baremo')->comment('N. Requerimiento Hrs. Comunitarias');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('pestudios', function (Blueprint $table) {
            $table->dropColumn('status_socials');
        });
    }
}
