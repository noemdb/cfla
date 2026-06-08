<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDateStatusBlacklistToEstudiantsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('estudiants', function (Blueprint $table) {
            $table->enum('status_blacklist',['true','false'])->after('status_active')->default('false')->comment('Lista negra');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('estudiants', function (Blueprint $table) {
            $table->dropColumn('status_blacklist');
        });
    }
}
