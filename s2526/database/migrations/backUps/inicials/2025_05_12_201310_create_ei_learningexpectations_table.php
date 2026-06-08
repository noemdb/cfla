<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEiLearningexpectationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('eilearningexpectations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('eilearningarea_id')->constrained()->onDelete('cascade')->comment('Área de aprendizaje');
            $table->text('description')->comment('Descripción del aprendizaje esperado');
            $table->longText('observations')->nullable()->comment('Observaciones');
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
        Schema::dropIfExists('eilearningexpectations');
    }
}
