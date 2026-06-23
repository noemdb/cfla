<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddStatusEmailToDebates extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('mailers', function (Blueprint $table) {
            $table->boolean('status_email')->nullable()->default(false)->after('status_test')->comment('Envio por email');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('mailers', function (Blueprint $table) {
            $table->dropColumn('status_email');
        });
    }
}
