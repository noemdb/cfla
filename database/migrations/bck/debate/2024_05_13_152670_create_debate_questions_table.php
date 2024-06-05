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
        Schema::create('debate_questions', function (Blueprint $table) {
            $table->smallIncrements('id')->comment('Clave primaria de la tabla');
            $table->unsignedInteger('debate_id')->comment('Clave foránea que referencia a la tabla de debates');
            $table->string('category')->comment('Categorías o áreas de conocimiento');
            $table->text('text')->comment('Texto de la pregunta');
            $table->text('observation')->nullable()->comment('Observación adicional para la pregunta');
            $table->boolean('status_active')->default(true)->comment('Estado de la pregunta (true/false)');
            $table->string('attachment')->nullable()->comment('Archivo adjunto para la pregunta');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('debate_questions');
    }
};
