<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDebateQuestionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('debate_questions', function (Blueprint $table) {
            $table->smallIncrements('id')->comment('Clave primaria de la tabla');
            $table->unsignedInteger('debate_id')->comment('Clave foránea que referencia a la tabla de debates');
            $table->string('category')->comment('Categorías o áreas de conocimiento');
            $table->integer('time')->comment('Tiempo para responder');
            $table->integer('time_elapsed')->nullable()->comment('Tiempo restante para responder');
            $table->text('text')->comment('Texto de la pregunta');
            $table->unsignedInteger('weighting')->comment('Ponderación');
            $table->text('observation')->nullable()->comment('Observación adicional para la pregunta');
            $table->integer('option_max')->default(4)->nullable()->comment('Máximo número de opciones');
            $table->boolean('status_active')->nullable()->comment('Estado de la pregunta (Activo/Deshabilitado)');
            $table->boolean('status_answer')->nullable()->comment('Estado de respuesta');
            $table->boolean('status_under_review')->nullable()->comment('En revisión');
            $table->string('attachment')->nullable()->comment('Archivo adjunto para la pregunta');
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
        Schema::dropIfExists('debate_questions');
    }
}
