<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEiprojectsummariesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('eiprojectsummaries', function (Blueprint $table) {
            $table->id(); // ID único para cada resumen del proyecto
            $table->unsignedBigInteger('eiprojectk_id'); // Proyecto de Aula
            $table->unsignedBigInteger('pevaluacion_id'); // Relación con el área de aprendizaje (evaluación)
            $table->string('componente')->nullable(); // Componente del área de aprendizaje
            $table->string('objetivo')->nullable(); // Objetivo del aprendizaje
            $table->text('aprendizaje_esperado')->nullable(); // Aprendizaje esperado
            $table->text('indicadores')->nullable(); // Indicadores de evaluación
            $table->string('linea_investigacion')->nullable(); // Línea de investigación (opcional)
            $table->string('enfasis_curriculares')->nullable(); // Énfasis curriculares (opcional)
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
        Schema::dropIfExists('eiprojectsummaries');
    }
}
