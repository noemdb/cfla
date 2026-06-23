<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMaxNumberEiplanningbwksToPeducativos extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('peducativos', function (Blueprint $table) {
            $table->unsignedInteger('max_number_eiplanningbwks')->after('show_quantitative_indicators')->nullable()->comment('Cantidad máxima de planes quincenales');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('peducativos', function (Blueprint $table) {
            $table->dropColumn('max_number_eiplanningbwks');
        });
    }
}
