<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDateBeginToPescolarsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pescolars', function (Blueprint $table) {
            $table->date('date_begin')->nullable()->after('date_work')->comment('Fecha de inicio de las actividades escolares');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('pescolars', function (Blueprint $table) {
            $table->dropColumn('date_begin');
        });
    }
}
