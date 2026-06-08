<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddObsResumenFinalToEstudiantsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('estudiants', function (Blueprint $table) {
            $table->string('obs_resumen_final')->after('status_active')->nullable()->comment('Observación para el resumen final');
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
            $table->dropColumn('obs_resumen_final');
        });
    }
}
