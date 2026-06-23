<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEiplanningbwstrategiesTable extends Migration
{
    public function up()
    {
        Schema::create('eiplanningbwstrategies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('eiplanningbwk_id')->constrained()->onDelete('cascade');
            $table->string('day_of_week'); // lunes, martes, miercoles, jueves, viernes
            $table->string('momento_rutina_diaria');
            $table->text('lunes')->nullable(); // Campo principal para la estrategia
            $table->text('martes')->nullable();
            $table->text('miercoles')->nullable();
            $table->text('jueves')->nullable();
            $table->text('viernes')->nullable();
            $table->integer('order')->nullable();
            $table->text('description')->nullable();
            $table->timestamps();

            // Índices simples individuales (nombres cortos)
            $table->index('eiplanningbwk_id');
            $table->index('day_of_week');
            $table->index('momento_rutina_diaria');
        });
    }

    public function down()
    {
        Schema::dropIfExists('eiplanningbwstrategies');
    }
}