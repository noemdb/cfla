<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTeachingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('teachings', function (Blueprint $table) {
            $table->bigIncrements('id')->comment('Identificador único del instrumento pedagógico (clave primaria)');
            $table->string('name', 191)->comment('Nombre del instrumento pedagógico');
            $table->text('description')->comment('Descripción detallada del instrumento pedagógico');
            $table->enum('type', ['evaluacion', 'aprendizaje', 'apoyo'])->comment('Tipo de instrumento pedagógico');
            $table->text('objective')->nullable()->comment('Objetivo del instrumento pedagógico');
            $table->text('continer')->nullable()->comment('Contenido');
            $table->unsignedInteger('author_id')->comment('Autor del instrumento pedagógico');
            $table->boolean('active')->default(true)->comment('Indica si el instrumento pedagógico está activo (1) o inactivo (0)');
            $table->string('url')->nullable()->comment('URL del instrumento pedagógico');
            $table->text('keywords')->nullable()->comment('Palabras clave para la búsqueda y clasificación');
            $table->unsignedInteger('curricular_area')->nullable()->comment('Área curricular a la que se asocia');
            $table->unsignedInteger('application_time')->nullable()->comment('Tiempo estimado de aplicación (minutos)');
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
        Schema::dropIfExists('teachings');
    }
}
