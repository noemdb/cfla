<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePromptContextsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('prompt_contexts', function (Blueprint $table) {
            $table->id(); // Primary key
            $table->string('code')->nullable()->comment('Code'); // Código
            $table->text('context_objective')->nullable()->comment('Stores the context or objective of the prompt, including educational and pedagogical focus.'); // Contexto/Objetivo
            $table->text('specific_requirements')->nullable()->comment('Details the specific requirements for the prompt, such as format, structure, and audience.'); // Requisitos Específicos
            $table->text('content')->nullable()->comment('Describes the main content to be included, focusing on the agreement, norms, and pedagogical recommendations.'); // Contenido
            $table->text('sources_precision')->nullable()->comment('Includes references to laws, decrees, or official norms, with general citations and warnings if outdated.'); // Fuentes y Precisión
            $table->text('style')->nullable()->comment('Defines the writing style, tone, and format requirements for the prompt.'); // Estilo
            $table->timestamps(); // Created at and updated at timestamps
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('prompt_contexts');
    }
}
