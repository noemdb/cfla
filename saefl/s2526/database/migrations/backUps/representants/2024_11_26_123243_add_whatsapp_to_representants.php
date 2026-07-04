<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddWhatsappToRepresentants extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('representants', function (Blueprint $table) {
            $table->string('whatsapp',32)->nullable()->after('phone')->comment('N.WhatsApp');
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
            $table->dropColumn('whatsapp');
        });
    }
}
