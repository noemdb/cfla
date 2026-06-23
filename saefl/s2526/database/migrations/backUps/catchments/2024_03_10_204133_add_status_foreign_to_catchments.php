<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddStatusForeignToCatchments extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('catchments', function (Blueprint $table) {
            $table->boolean('status_foreign')->nullable()->before('date_birth')->default(false)->comment('¿Procede del Extranjero?');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('catchments', function (Blueprint $table) {
            $table->dropColumn('status_foreign');
        });
    }
}
