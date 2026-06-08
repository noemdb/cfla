<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddStatusInscripcionActiveToPestudios extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pestudios', function (Blueprint $table) {
            $table->boolean('status_inscripcion_active')->nullable()->default(true)->after('status_active')->default(false)->comment('Activo para la matriculación escolar');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('pestudios', function (Blueprint $table) {
            $table->dropColumn('status_inscripcion_active');
        });
    }
}
