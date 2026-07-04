<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCategoryToIncidentReasons extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('incident_reasons', function (Blueprint $table) {
            $table->string('category')->after('name')->nullable()->comment('Categoría');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('incident_reasons', function (Blueprint $table) {
            $table->dropColumn('category');
        });
    }
}
