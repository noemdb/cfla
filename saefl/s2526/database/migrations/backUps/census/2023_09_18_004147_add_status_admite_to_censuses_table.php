<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddStatusAdmiteToCensusesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('censuses', function (Blueprint $table) {
            $table->enum('status_admite',['true','false'])->after('instagram')->default('false')->comment('Admitido');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('censuses', function (Blueprint $table) {
            $table->dropColumn('status_admite');
        });
    }
}
