<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddStatusOfficialToPevaluacions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pevaluacions', function (Blueprint $table) {
            $table->boolean('status_official')->default(true)->after('status_baremo')->comment('En documentos oficiales');
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
            $table->dropColumn('status_official');
        });
    }
}
