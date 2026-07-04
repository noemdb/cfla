<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPestudioIdToMailersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('mailers', function (Blueprint $table) {
            $table->integer('pestudio_id')->unsigned()->after('grado_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('mailers', function (Blueprint $table) {
            $table->dropColumn('pestudio_id');
        });
    }
}
