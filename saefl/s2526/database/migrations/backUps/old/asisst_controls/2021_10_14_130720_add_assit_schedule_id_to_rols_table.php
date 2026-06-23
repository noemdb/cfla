<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAssitScheduleIdToRolsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('rols', function (Blueprint $table) {
            $table->unsignedBigInteger('assit_schedule_id')->after('group')->nullable()->comment('Horario Asignado');
            $table->foreign('assit_schedule_id')->references('id')->on('assit_schedules')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('rols', function (Blueprint $table) {
            $table->dropColumn('number_id');
        });
    }
}
