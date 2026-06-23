<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDateAnnouncementToIncidentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('incidents', function (Blueprint $table) {
            $table->time('hour_announcement')->nullable()->after('status_announcement')->comment('Hora Programada');
            $table->date('date_announcement')->nullable()->after('status_announcement')->comment('Fecha Programada');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('incidents', function (Blueprint $table) {
            $table->dropColumn('date_announcement');
            $table->dropColumn('hour_announcement');
        });
    }
}
