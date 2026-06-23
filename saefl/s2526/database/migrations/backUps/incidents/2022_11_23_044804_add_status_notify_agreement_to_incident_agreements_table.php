<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddStatusNotifyAgreementToIncidentAgreementsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('incident_agreements', function (Blueprint $table) {
            $table->enum('status_notify_agreement',['true','false'])->after('observations')->default('false')->comment('Cuenta incobrable');
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
            $table->dropColumn('status_notify_agreement');
        });
    }
}
