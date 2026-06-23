<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DropRepresentantIdFromPollTokensTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('poll_tokens', function (Blueprint $table) {
            $table->dropColumn(['representant_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('poll_tokens', function (Blueprint $table) {
            $table->bigInteger('representant_id')->unsigned()->nullable()->after('poll_main_id')->comment('Representate');
        });
    }
}
