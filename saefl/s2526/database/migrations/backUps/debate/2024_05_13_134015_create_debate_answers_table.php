<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDebateAnswersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('debate_answers', function (Blueprint $table) {
            $table->id()->comment('Clave primaria de la tabla');
            $table->unsignedInteger('question_id')->comment('Clave foránea que referencia a la tabla de preguntas');
            $table->unsignedInteger('option_id')->comment('Clave foránea que referencia a la tabla de opciones');
            $table->unsignedInteger('grado_id')->nullable()->comment('Clave foránea que referencia a la tabla de grados');
            $table->unsignedInteger('seccion_id')->nullable()->comment('Clave foránea que referencia a la tabla de secciones');
            $table->boolean('status_claim')->nullable()->comment('Estado de la respuesta (Activo/Deshabilitado)');
            $table->unsignedInteger('score')->comment('Puntaje obtenido por la sección al responder esta pregunta');
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
        Schema::dropIfExists('debate_answers');
    }
}
