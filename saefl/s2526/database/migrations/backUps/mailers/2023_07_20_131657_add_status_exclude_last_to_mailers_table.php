<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddStatusExcludeLastToMailersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('mailers', function (Blueprint $table) {
            $table->enum('status_exclude_last',['true','false'])->after('status_test')->default('false')->comment('Excluye el último curso');
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
            $table->dropColumn('status_exclude_last');
        });
    }
}
