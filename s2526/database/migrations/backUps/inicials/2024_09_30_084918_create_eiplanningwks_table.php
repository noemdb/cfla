<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEiplanningwksTable extends Migration
{
    /**
     * Run the migrations. Planificación Semanal datos de cabecera
     *
     * @return void
     */
    public function up()
    {
        Schema::create('eiplanningwks', function (Blueprint $table) {
            $table->id(); // ID único para la planificación
            $table->integer('profesor_id')->unsigned(); // Nombre del docente
            $table->integer('grado_id')->unsigned(); // Grupo asignado
            $table->integer('seccion_id')->unsigned(); // Sección asignado
            $table->date('finicial'); // Fecha de inicio de la planificación
            $table->date('ffinal'); // Fecha de culminación de la planificación
            $table->integer('tiempo_ejecucion')->unsigned(); // Tiempo de ejecución en semanas
            $table->text('diagnostico')->nullable(); // Diagnóstico inicial para el grupo
            $table->text('observacion')->nullable(); // Observaciones adicionales del docente (opcional)
            $table->timestamps(); // created_at y updated_at
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('eiplanningwks');
    }
}
