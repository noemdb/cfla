<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEievaluationksTable extends Migration
{
    /**
     * Run the migrations. Plan de Evaluacion, datos de cabecera
     *
     * @return void
     */
    public function up()
    {
        Schema::create('eievaluationks', function (Blueprint $table) {
            $table->id(); // ID único del informe pedagógico
            $table->unsignedBigInteger('profesor_id'); // Profesor
            $table->unsignedBigInteger('grado_id'); // Grado
            $table->unsignedBigInteger('lapso_id'); // Momento
            $table->unsignedBigInteger('seccion_id'); // Sección
            $table->date('finicial'); // Fecha inicial
            $table->date('ffinal'); // Fecha final
            $table->text('observaciones')->nullable(); // Observaciones del docente sobre el estudiante
            $table->text('recomendacion')->nullable(); // Recomendación del docente
            $table->text('asistencia')->nullable(); // Control de Asistencia
            $table->text('observacion')->nullable(); // Observaciones adicionales del docente (opcional)
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
        Schema::dropIfExists('eievaluationks');
    }
}
