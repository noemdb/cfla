<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPestudioIdToDiagReferentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('diag_referents', function (Blueprint $table) {
            $table->integer('pestudio_id')->unsigned()->nullable()->after('id');
            $table->foreign('pestudio_id')->references('id')->on('pestudios');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('diag_referents', function (Blueprint $table) {
            $table->dropForeign(['pestudio_id']);
            $table->dropColumn('pestudio_id');
        });
    }
}
