<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAssitAttendanceTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('assit_attendances', function (Blueprint $table) {
            $table->id();
			$table->string('user')->comment('Nombre de Usuario');
			$table->string('work_id')->comment('Identificador del Trabajado');
			$table->string('card_id')->comment('Num. de Tarjeta');
			$table->string('date')->comment('fecha');
			$table->string('time')->comment('fecha');
			$table->string('in_out')->comment('Entrada/Salida');
			$table->string('event_code')->comment('Evento');
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
        Schema::dropIfExists('assit_attendance');
    }
}
