<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('activities', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('pevaluacion_id')->comment('Plan de Evaluación');
            $table->date('finicial')->comment('Fecha Inicial');
            $table->date('ffinal')->comment('Fecha Final');
            $table->text('topic')->nullable()->comment('Tema');
            $table->text('thematic')->nullable()->comment('Tejido temático');
            $table->text('references')->comment('Referentes teórico práticos');
            $table->text('teaching')->comment('Enseñanza');
            $table->text('learning')->comment('Aprendizaje');
            $table->text('description')->nullable()->comment('Descripción');
            $table->text('observations')->comment('Observaciones');
            $table->text('comments')->nullable()->comment('Comentarios');
            $table->boolean('status')->default(false)->comment('Aprobación');
            $table->timestamps();

            $table->foreign('pevaluacion_id')->references('id')->on('pevaluacions')
                  ->cascadeOnUpdate()->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('activities');
    }
};
