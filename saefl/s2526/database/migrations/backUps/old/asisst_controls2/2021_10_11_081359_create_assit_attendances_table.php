<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAssitAttendancesTable extends Migration
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
			$table->string('card_id')->nullable()->comment('Num. de Tarjeta');
			$table->string('date')->comment('fecha');
			$table->string('time')->comment('hora');
			$table->string('timestamp',32)->unique();
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
        Schema::dropIfExists('assit_attendances');
    }
}
