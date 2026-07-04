<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddLaborDateToPescolarsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pescolars', function (Blueprint $table) {
            $table->date('date_work')->nullable()->comment('Fecha de inicio laboral')->after('ffinal');
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
            $table->dropColumn('date_work');
        });
    }
}
