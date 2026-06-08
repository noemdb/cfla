<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEifinalksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('eifinalks', function (Blueprint $table) {
            $table->id();

            // Relaciones principales
            $table->foreignId('pevaluacion_id')->constrained()->onDelete('cascade')->comment('Período de evaluación');
            $table->foreignId('estudiant_id')->constrained()->onDelete('cascade')->comment('Estudiante');

            // Metadatos
            $table->string('title')->comment('Título del informe');

            // Contenido del boletín
            $table->text('context_group')->nullable()->comment('Apreciación del estudiante, características, necesidades');
            $table->longText('planing_eject')->nullable()->comment('Resumen de la planificación ejecutada');
            $table->longText('featured_project')->nullable()->comment('Descripción del proyecto más significativo');
            $table->longText('special_activities')->nullable()->comment('Eventos especiales');
            $table->longText('achievements')->nullable()->comment('Logros del estudiante');
            $table->longText('individual_observations')->nullable()->comment('Observaciones socioafectivas');
            $table->longText('family_participation')->nullable()->comment('Participación familiar');
            $table->longText('conclusions')->nullable()->comment('Reflexión final del docente');
            $table->longText('recommendations')->nullable()->comment('Sugerencias a la familia y equipo docente');

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
        Schema::dropIfExists('eifinalks');
    }
}
