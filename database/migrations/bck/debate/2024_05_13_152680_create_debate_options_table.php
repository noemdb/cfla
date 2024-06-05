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
        Schema::create('debate_options', function (Blueprint $table) {
            $table->smallIncrements('id')->comment('Clave primaria de la tabla');
            $table->unsignedInteger('question_id')->comment('Clave foránea que referencia a la tabla de preguntas');
            $table->text('text')->comment('Texto de la opción');
            $table->text('observation')->nullable()->comment('Observación adicional para la pregunta');
            $table->string('attachment')->nullable()->comment('Archivo adjunto para la opción');
            $table->boolean('status_option_correct')->default(false)->comment('Opción correcta (true/false)');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('debate_options');
    }
};
