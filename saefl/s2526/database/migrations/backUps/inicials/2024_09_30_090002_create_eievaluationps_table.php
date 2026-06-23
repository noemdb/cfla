<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEievaluationpsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('eievaluationps', function (Blueprint $table) {
            $table->id(); // ID único del plan de evaluación
            $table->unsignedBigInteger('eievaluationk_id'); // Proyecto de Aula
            $table->unsignedBigInteger('pevaluacion_id'); // Área de aprendizaje
            $table->date('fecha')->nullable(); // Fecha de evaluación
            $table->string('nombre_ninos')->nullable(); // Nombre de los niños
            $table->text('aprendizaje_alcanzado')->nullable(); // Aprendizaje a ser alcanzado
            $table->string('componente')->nullable(); // Componente del área de aprendizaje
            $table->text('indicadores')->nullable(); // Indicadores de evaluación
            $table->text('instrumento')->nullable(); // Instrumento de evaluación
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
        Schema::dropIfExists('eievaluationps');
    }
}
