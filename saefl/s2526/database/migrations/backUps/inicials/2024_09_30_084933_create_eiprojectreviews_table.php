<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEiprojectreviewsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('eiprojectreviews', function (Blueprint $table) {
            $table->id(); // ID único para cada revisión del proyecto
            $table->unsignedBigInteger('eiprojectk_id'); // Relación con la tabla eiprojectks (proyecto de aula)
            $table->text('posibles_temas_interes')->nullable(); // Posibles temas de interés
            $table->text('eleccion_tema_nombre')->nullable(); // Elección del tema y nombre del proyecto
            $table->text('que_sabe')->nullable(); // Qué saben los estudiantes
            $table->text('que_desean_aprender')->nullable(); // Qué desean aprender los estudiantes
            $table->text('que_necesitamos')->nullable(); // Qué necesitamos para el proyecto
            $table->text('quienes_nos_pueden_apoyar')->nullable(); // Quiénes nos pueden apoyar
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
        Schema::dropIfExists('eiprojectreviews');
    }
}
