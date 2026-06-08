<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAssitSchedulesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('assit_schedules', function (Blueprint $table) {
            $table->id();
			$table->string('name')->comment('Nombre');
			$table->smallInteger('number_turn')->unsigned();
			$table->text('description')->comment('Descripción');
			$table->text('observations')->nullable()->comment('Observación');
			$table->enum('frecuency',['SEMANAL','QUINCENAL','MENSUAL'])->default('SEMANAL')->comment('Frecuencia');
			$table->enum('status',['true','false'])->default('true')->comment('Estado');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('assit_schedules');
    }
}
