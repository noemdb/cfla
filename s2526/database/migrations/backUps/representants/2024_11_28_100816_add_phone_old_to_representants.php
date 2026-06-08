<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPhoneOldToRepresentants extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('representants', function (Blueprint $table) {
            $table->string('phone_old',32)->nullable()->after('phone')->comment('N. de teléfono antíguo');
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
            $table->dropColumn('phone_old');
        });
    }
}
