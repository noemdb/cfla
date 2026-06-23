<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddGrupoEstableIdToPevaluacions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pevaluacions', function (Blueprint $table) {
            $table->unsignedInteger('grupo_estable_id')->after('pensum_id')->nullable()->comment('Grupo Estable');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('pevaluacions', function (Blueprint $table) {
            $table->dropColumn('grupo_estable_id');
        });
    }
}
