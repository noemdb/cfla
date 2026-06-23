<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddStatusLastToLapsos extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('lapsos', function (Blueprint $table) {
            $table->enum('status_last',['true','false'])->after('time_preclosing')->default('false')->comment('Permite carga de notas extenporáneas');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('lapsos', function (Blueprint $table) {
            $table->dropColumn('status_last');
        });
    }
}
