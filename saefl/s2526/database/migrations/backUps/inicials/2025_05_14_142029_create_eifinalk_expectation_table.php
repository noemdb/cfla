<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEifinalkExpectationTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('eifinalk_expectation', function (Blueprint $table) {
            $table->id();

            $table->foreignId('eifinalk_id')->constrained()->onDelete('cascade')->comment('Informe pedagógico');
            $table->foreignId('eilearningarea_id')->constrained()->onDelete('cascade')->comment('Área de aprendizaje');
            $table->foreignId('eilearningexpectation_id')->constrained()->onDelete('cascade')->comment('Aprendizaje esperado');
            $table->foreignId('pevaluacion_id')->constrained()->onDelete('cascade')->comment('Área de Formación');

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
        Schema::dropIfExists('eifinalk_expectation');
    }
}
