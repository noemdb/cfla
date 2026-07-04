<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEiprojectkstrategiesTable extends Migration
{
    public function up()
    {
        Schema::create('eiprojectkstrategies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('eiprojectk_id')->constrained()->onDelete('cascade');
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

            // Índices con nombres cortos
            $table->index(['eiprojectk_id', 'day_of_week', 'momento_rutina_diaria'], 'projectk_strat_day_moment');
            $table->index(['eiprojectk_id', 'momento_rutina_diaria'], 'projectk_strat_moment');
        });
    }

    public function down()
    {
        Schema::dropIfExists('eiprojectkstrategies');
    }
}