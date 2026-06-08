<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddStatusWhatsappVerifyToRepresentants extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('representants', function (Blueprint $table) {
            $table->boolean('status_whatsapp_verify')->nullable()->default(false)->after('whatsapp')->comment('WhatsApp verificada');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('representants', function (Blueprint $table) {
            $table->dropColumn('status_whatsapp_verify');
        });
    }
}
