<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddStatusAcceptTermsToCatchments extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('catchments', function (Blueprint $table) {
            $table->boolean('status_accept_terms')->nullable()->before('status_active')->default(false)->comment('¿Tiene hermanos estudiando en el Colegio?');
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
            $table->dropColumn('status_accept_terms');
        });
    }
}
