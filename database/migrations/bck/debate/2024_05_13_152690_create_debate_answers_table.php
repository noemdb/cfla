<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('debate_answers', function (Blueprint $table) {
            $table->smallIncrements('id')->comment('Clave primaria de la tabla');
            $table->unsignedInteger('question_id')->comment('Clave foránea que referencia a la tabla de preguntas');
            $table->unsignedInteger('option_id')->comment('Clave foránea que referencia a la tabla de opciones');
            $table->unsignedInteger('grado_id')->comment('Clave foránea que referencia a la tabla de grados');
            $table->unsignedInteger('seccion_id')->comment('Clave foránea que referencia a la tabla de secciones');
            $table->boolean('status_claim')->default(false)->comment('Estado de la respuesta (true/false)');
            $table->unsignedInteger('score')->comment('Puntaje obtenido por la sección al responder esta pregunta');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('debate_answers');
    }
};
