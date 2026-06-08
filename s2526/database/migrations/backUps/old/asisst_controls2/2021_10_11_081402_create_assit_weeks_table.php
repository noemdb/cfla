<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAssitWeeksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('assit_weeks', function (Blueprint $table) {
            $table->id();
			$table->unsignedBigInteger('assit_schedule_id');
			$table->string('name')->comment('Nombre');
			$table->smallInteger('number_week')->comment('Número de semana');
            $table->timestamps();
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
        Schema::dropIfExists('assit_weeks');
    }
}
