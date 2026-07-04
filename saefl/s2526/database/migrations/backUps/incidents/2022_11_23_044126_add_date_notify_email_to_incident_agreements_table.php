<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDateNotifyEmailToIncidentAgreementsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('incident_agreements', function (Blueprint $table) {
            $table->date('date_notify_email')->nullable()->after('observations')->comment('Fecha de notificación');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('incident_agreements', function (Blueprint $table) {
            $table->dropColumn('date_notify_email');
        });
    }
}
