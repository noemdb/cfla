<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMaxNumberEievaluationksToPeducativos extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('peducativos', function (Blueprint $table) {
            $table->unsignedInteger('max_number_eievaluationks')->after('show_quantitative_indicators')->nullable()->comment('Cantidad máxima de evaluaciones');
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
            $table->dropColumn('max_number_eievaluationks');
        });
    }
}
