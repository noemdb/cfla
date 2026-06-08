<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DropStatusNotifiedFromPollMainsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('poll_mains', function (Blueprint $table) {
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
        Schema::table('poll_mains', function (Blueprint $table) {
            $table->enum('status_notified',['true','false'])->after('time_end')->default('false')->comment('Estado de la notificación');
        });
    }
}
