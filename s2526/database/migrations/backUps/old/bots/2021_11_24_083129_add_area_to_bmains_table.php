<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAreaToBmainsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('bmains', function (Blueprint $table) {
            $table->enum('area',['SISTEMA','DIRECCION','AUTORIDAD','ADMINISTRACION','CONTROL ESTUDIO','PROFESORADO','ESTUDIANTIL','REPRESENTANTE'])->nuleable()->after('name');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('bmains', function (Blueprint $table) {
            $table->dropColumn('area');
        });
    }
}
