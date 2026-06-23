<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMailCcAddressToAutoridadsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('autoridads', function (Blueprint $table) {
            $table->string('mail_cc_address')->after('profile_professional')->nullable()->comment('Dirección de Correo CC ECA');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('autoridads', function (Blueprint $table) {
            $table->dropColumn('mail_cc_address');
        });
    }
}
