<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDebateOptionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('debate_options', function (Blueprint $table) {
            $table->smallIncrements('id')->comment('Clave primaria de la tabla');
            $table->unsignedInteger('question_id')->comment('Clave foránea que referencia a la tabla de preguntas');
            $table->text('text')->comment('Texto de la opción');
            $table->text('observation')->nullable()->comment('Observación adicional para la pregunta');
            $table->string('attachment')->nullable()->comment('Archivo adjunto para la opción');
            $table->boolean('status_option_correct')->nullable()->comment('Opción correcta (Activo/Deshabilitado)');
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
        Schema::dropIfExists('debate_options');
    }
}
