<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddStatusActiveToPensums extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pensums', function (Blueprint $table) {
            $table->boolean('status_active')->nullable()->default(true)->after('status_component')->comment('Activo');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('pensums', function (Blueprint $table) {
            $table->dropColumn('status_active');
        });
    }
}
