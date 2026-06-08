<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEiplanningwsummariesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('eiplanningwsummaries', function (Blueprint $table) {
            $table->id(); // ID único para cada resumen
            $table->unsignedBigInteger('eiplanningwk_id'); // Relación con la planificación semanal
            $table->unsignedBigInteger('pevaluacion_id')->nullable(); // Área de aprendizaje
            $table->text('componente')->nullable(); // Componente
            $table->text('objetivo')->nullable(); // Objetivo
            $table->text('aprendizaje_esperado')->nullable(); // Aprendizaje esperado
            $table->text('indicadores')->nullable(); // Indicadores
            $table->text('linea_investigacion')->nullable(); // Línea de investigación
            $table->text('enfasis_curriculares')->nullable(); // Énfasis curriculares
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
        Schema::dropIfExists('eiplanningwsummaries');
    }
}
