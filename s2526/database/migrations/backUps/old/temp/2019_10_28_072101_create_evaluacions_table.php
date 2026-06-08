<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEvaluacionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('evaluacions', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('pevaluacion_id')->unsigned()->comment('Contenido');
            $table->integer('escala_id')->unsigned()->comment('Contenido');
            // $table->integer('contenido_id')->unsigned()->comment('Contenido');
            // $table->string('objetivo')->nullable()->comment('Objetivo');
            // $table->enum('tipo',['NUMÉRICA','CUALITATIVA','NUMERICA Y CUALITATIVA'])->comment('Tipo');
            $table->string('objetivo')->nullable()->comment('Objetivo');
            $table->string('description')->nullable()->comment('Descripción');
            $table->string('observations')->nullable()->comment('Observaciones');

            $table->softDeletes();
            $table->timestamps();
            
            $table->foreign('pevaluacion_id')->references('id')->on('pevaluacions')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('escala_id')->references('id')->on('escalas')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('evaluacions');
    }
}
