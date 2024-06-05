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
        Schema::create('debates', function (Blueprint $table) {
            $table->smallIncrements('id')->comment('Clave primaria de la tabla');
            $table->unsignedSmallInteger('competition_id')->comment('Clave foránea que referencia a la tabla de competiciones');
            $table->unsignedInteger('grado_id')->comment('Clave foránea que referencia a la tabla de grados');
            $table->unsignedInteger('seccion_id')->comment('Clave foránea que referencia a la tabla de secciones');
            $table->unsignedInteger('winner_section_id')->nullable()->comment('Clave foránea que referencia a la tabla de secciones para almacenar la sección ganadora');
            $table->string('name')->comment('Nombre del debate');
            $table->text('description')->comment('Descripción del debate');
            $table->boolean('status_active')->default(true)->comment('Estado del debate (true/false)');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('debates');
    }
};
