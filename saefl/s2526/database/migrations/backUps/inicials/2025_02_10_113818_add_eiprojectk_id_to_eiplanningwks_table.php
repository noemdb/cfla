<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddEiprojectkIdToEiplanningwksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('eiplanningwks', function (Blueprint $table) {
            $table->unsignedInteger('eiprojectk_id')->after('seccion_id')->nullable()->comment('Proyecto vinculado');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('eiplanningwks', function (Blueprint $table) {
            $table->dropColumn('eiprojectk_id');
        });
    }
}
