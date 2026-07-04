<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DropStatusNotifiedFromPollTokensTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('poll_tokens', function (Blueprint $table) {
            $table->dropColumn(['status_notified']);
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
            $table->enum('status_notified',['true','false'])->after('token')->default('false')->comment('Estado de la notificación');
        });
    }
}
