<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AlterTableRolsChangeArea extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('rols', function (Blueprint $table) {
            DB::statement("ALTER TABLE `rols` CHANGE `area` `area` ENUM('SISTEMA','DIRECCION','AUTORIDAD','ADMINISTRACION','CONTROL ESTUDIO','PROFESORADO','ESTUDIANTIL','REPRESENTANTE','ACADEMICO','ADMINISTRATIVO','BIENESTAR','EVALUACION','PROYECTO') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'ESTUDIANTIL';");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('rols', function (Blueprint $table) {
            DB::statement("ALTER TABLE `rols` CHANGE `area` `area` ENUM('SISTEMA','DIRECCION','AUTORIDAD','ADMINISTRACION','CONTROL ESTUDIO','PROFESORADO','ESTUDIANTIL','REPRESENTANTE','ACADEMICO','ADMINISTRATIVO','BIENESTAR') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'ESTUDIANTIL';");
        });
    }
}
