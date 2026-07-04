<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMailLogsToMaileds extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('maileds', function (Blueprint $table) {
            $table->string('service_provider')->nullable();
            $table->unsignedBigInteger('mail_log_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('maileds', function (Blueprint $table) {
            $table->dropColumn('service_provider');
            $table->dropColumn('mail_log_id');
        });
    }
}
