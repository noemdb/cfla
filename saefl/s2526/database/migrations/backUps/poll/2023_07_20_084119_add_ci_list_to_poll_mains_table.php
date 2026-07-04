<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCiListToPollMainsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('poll_mains', function (Blueprint $table) {
            $table->string('ci_list')->nullable()->after('time_end')->comment('Lista de CI.');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('poll_mains', function (Blueprint $table) {
            $table->dropColumn('ci_list');
        });
    }
}
