<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddStatusSiblingsCollegeToCatchments extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('catchments', function (Blueprint $table) {
            $table->boolean('status_siblings_college')->nullable()->before('country_foreign')->default(false)->comment('¿Tiene hermanos estudiando en el Colegio?');
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
            $table->dropColumn('status_siblings_college');
        });
    }
}
