<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDeputyIdToPeducativosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('peducativos', function (Blueprint $table) {
            $table->unsignedInteger('deputy_id')->after('manager_id')->nullable()->comment('Adjunto');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('peducativos', function (Blueprint $table) {
            $table->dropColumn('deputy_id');
        });
    }
}
