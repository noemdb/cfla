<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCountryForeignToCatchments extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('catchments', function (Blueprint $table) {
            $table->string('country_foreign')->nullable()->after('status_foreign')->comment('País de procedencia');
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
            $table->dropColumn('country_foreign');
        });
    }
}
