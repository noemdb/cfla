<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddStatusCargaNotasToPestudios extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pestudios', function (Blueprint $table) {
            $table->enum('status_carga_notas',['true','false'])->after('status_active')->default('false')->comment('Permite carga de notas extenporáneas');
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
            $table->dropColumn('status_carga_notas');
        });
    }
}
