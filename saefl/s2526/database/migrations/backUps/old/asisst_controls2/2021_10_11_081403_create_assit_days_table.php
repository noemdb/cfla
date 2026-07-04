<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAssitDaysTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('assit_days', function (Blueprint $table) {
            $table->id();
			$table->unsignedBigInteger('assit_week_id');
			$table->string('name')->comment('Nombre');
			$table->smallInteger('number_day')->comment('Número del día');
            $table->timestamps();
            $table->foreign('assit_week_id')->references('id')->on('assit_weeks')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('assit_days');
    }
}
