<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEispecialkStrategiesTable extends Migration
{
    public function up()
    {
        Schema::create('eispecialstrategies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('eispecialk_id')->constrained()->onDelete('cascade');
            $table->string('day_of_week');
            $table->string('momento_rutina_diaria');
            $table->text('lunes')->nullable();
            $table->text('martes')->nullable();
            $table->text('miercoles')->nullable();
            $table->text('jueves')->nullable();
            $table->text('viernes')->nullable();
            $table->integer('order')->nullable();
            $table->text('description')->nullable();
            $table->timestamps();

            // Índices con nombres cortos
            $table->index(['eispecialk_id', 'day_of_week', 'momento_rutina_diaria'], 'specialk_strat_day_moment');
            $table->index(['eispecialk_id', 'momento_rutina_diaria'], 'specialk_strat_moment');
        });
    }

    public function down()
    {
        Schema::dropIfExists('eispecialkstrategies');
    }
}