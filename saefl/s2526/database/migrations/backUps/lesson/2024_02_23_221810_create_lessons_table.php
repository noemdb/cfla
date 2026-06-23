<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLessonsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('lessons', function (Blueprint $table) {
            $table->bigIncrements('id');
            //Profesor
            $table->unsignedInteger('evaluacion_id')->comment('Referentes teórico-prácticos');
            $table->unsignedTinyInteger('order')->comment('Orden de la lección dentro del referente teórico-práctico');
            $table->string('title')->comment('Título');
            $table->boolean('status')->default(false)->comment('¿Está finalizada?');
            $table->string('comments')->nullable()->comment('Comentarios del profesor al finalizar');
            $table->string('evidence')->nullable()->comment('Evidencia fotográfica');
            $table->text('requireds')->nullable()->comment('Materiales requeridos');            

            //Adicionales
            $table->text('description')->nullable()->comment('Descripción detallada');
            $table->string('objectives')->nullable()->comment('Objetivos específicos');
            $table->enum('activity_type', ['Teórica', 'Práctica', 'Laboratorio','Proyecto final','Exhibición'])->default('Teórica')->comment('Tipo de actividad');
            $table->unsignedTinyInteger('duration')->nullable()->comment('Duración (minutos)');
            $table->date('reprogrammed')->nullable()->comment('Fecha reprogramada');

            //Supervisor
            $table->text('observations')->nullable()->comment('Observaciones del Supervisor');
            $table->boolean('active')->default(true)->comment('¿Está activa?');
            
            $table->enum('level', ['Fácil', 'Intermedio', 'Difícil'])->default('Fácil')->comment('Nivel de dificultad');
            $table->date('planned')->comment('Fecha planeada');            
            
            //system
            $table->unsignedInteger('pedagogical_id')->nullable()->comment('ID de instrumentos pedagógicos a utilizar');
            $table->unsignedInteger('reprogrammed_by')->nullable()->comment('Usuario que reprogramó la lección');
            $table->unsignedInteger('author_id')->comment('Autor');
            $table->unsignedInteger('modified_by')->nullable()->comment('Usuario que modificó la lección por última vez');

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('lessons');
    }
}
