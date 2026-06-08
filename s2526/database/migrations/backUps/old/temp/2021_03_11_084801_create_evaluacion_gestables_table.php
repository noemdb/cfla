<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEvaluacionGestablesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('evaluacion_gestables', function (Blueprint $table) {
            $table->smallIncrements('id');
            $table->smallinteger('profesor_gestable_id')->unsigned()->comment('Asignacion de Profesor');
            $table->bigInteger('evaluacion_id')->unsigned()->comment('Evaluación');

            $table->foreign('profesor_gestable_id')->references('id')->on('profesor_gestables')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('evaluacion_id')->references('id')->on('evaluacions')->onDelete('cascade')->onUpdate('cascade');
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
        Schema::dropIfExists('evaluacion_gestables');
    }
}
