<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddStatusActiveToGrupoEstables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('grupo_estables', function (Blueprint $table) {
            $table->enum('status_active',['true','false'])->after('status_belongs_ins')->default('false')->comment('Estado (Activo/Inactivo)');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('grupo_estables', function (Blueprint $table) {
            $table->dropColumn('status_active');
        });
    }
}
