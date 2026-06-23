<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAssitHoursTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('assit_hours', function (Blueprint $table) {
            $table->id();
			$table->unsignedBigInteger('assit_turn_id');
			$table->smallInteger('h')->unsigned()->comment('Hora');
			$table->smallInteger('m')->unsigned()->comment('Minutos');
			$table->boolean('type')->comment('Entrada/Salida');
            $table->timestamps();
			$table->foreign('assit_turn_id')->references('id')->on('assit_turns')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('assit_hours');
    }
}
