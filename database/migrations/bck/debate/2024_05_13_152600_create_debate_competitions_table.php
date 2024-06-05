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
        Schema::create('debate_competitions', function (Blueprint $table) {
            $table->smallIncrements('id')->comment('Clave primaria de la tabla');
            $table->unsignedInteger('user_id')->comment('Clave foránea que referencia al usuario que creó la competición');
            $table->string('name')->comment('Nombre de la competición');
            $table->string('token')->comment('Ident. de acceso');
            $table->text('description')->comment('Descripción de la competición');
            $table->text('motive')->comment('Motivo de la competición');
            $table->date('date')->comment('Fecha de la competición');
            $table->boolean('status_active')->nullable()->comment('Estado de la competición (Activo/Deshabilitado)');
            $table->string('attachment')->nullable()->comment('Archivo adjunto a la competición');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('debate_competitions');
    }
};
