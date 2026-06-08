<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProfesorGestablesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('profesor_gestables', function (Blueprint $table) {
            $table->smallIncrements('id');
            $table->integer('profesor_id')->unsigned()->comment('Profesor');
            $table->bigInteger('pevaluacion_id')->unsigned()->comment('Asignatura');
            $table->integer('grupo_estable_id')->unsigned()->comment('Grupo Estable');
            $table->string('observations')->nullable()->comment('Observaciones');

            $table->timestamps();

            $table->foreign('profesor_id')->references('id')->on('profesors')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('pevaluacion_id')->references('id')->on('pevaluacions')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('grupo_estable_id')->references('id')->on('grupo_estables')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('profesor_gestables');
    }
}
