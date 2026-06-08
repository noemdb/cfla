<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDebatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('debates', function (Blueprint $table) {
            $table->smallIncrements('id')->comment('Clave primaria de la tabla');
            $table->unsignedSmallInteger('competition_id')->comment('Clave foránea que referencia a la tabla de competiciones');
            $table->string('token')->comment('Ident. de acceso');
            $table->unsignedInteger('grado_id')->nullable()->comment('Clave foránea que referencia a la tabla de grados');
            $table->unsignedInteger('seccion_id')->nullable()->comment('Clave foránea que referencia a la tabla de secciones');
            $table->unsignedInteger('winner_section_id')->nullable()->comment('Clave foránea que referencia a la tabla de secciones para almacenar la sección ganadora');
            $table->string('name')->comment('Nombre del debate');
            $table->text('description')->comment('Descripción del debate');
            $table->integer('question_max')->nullable()->default(5)->comment('Máximo número de preguntas por categoría');
            $table->boolean('status_active')->nullable()->comment('Estado del debate (Activo/Deshabilitado)');
            $table->string('attachment')->nullable()->comment('Archivo adjunto al debate');
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
        Schema::dropIfExists('debates');
    }
}
