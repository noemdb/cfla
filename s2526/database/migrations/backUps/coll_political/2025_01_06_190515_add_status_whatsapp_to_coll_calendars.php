<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddStatusWhatsappToCollCalendars extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('coll_calendars', function (Blueprint $table) {
            $table->boolean('status_whatsapp')->nullable()->default(false)->after('status_active')->comment('Envio por WhatsApp');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('coll_calendars', function (Blueprint $table) {
            $table->dropColumn('status_whatsapp');
        });
    }
}
