<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddStatusNoticeToEstudiants extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('estudiants', function (Blueprint $table) {
            $table->boolean('status_notice')->nullable()->default(false)->after('status_active')->comment('Considerado para el envío de notificaciones');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('estudiants', function (Blueprint $table) {
            $table->dropColumn('status_notice');
        });
    }
}
