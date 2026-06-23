<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRegistroTitulosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('registro_titulos', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->smallinteger('institucion_id')->unsigned();
            $table->integer('pestudio_id')->unsigned()->comment('Plan Estudio');
            $table->integer('grado_id')->unsigned()->comment('Grado del Plan de Estudio');
            $table->smallinteger('tevaluacion_id')->nullable()->unsigned()->comment('Tipo de Evaluación');
            $table->string('tipo')->comment('Tipo de Registro');
            $table->string('name')->comment('Nombre del Documento');
            $table->string('fecha_egreso')->comment('Fecha Egreso');
            $table->string('code')->comment('Código del Formato');
            $table->string('funcionario_ci')->comment('Cédula del funcionario gubernamental');
            $table->string('funcionario_name')->comment('Nombre del funcionario gubernamental');
            $table->softDeletes();
            $table->timestamps();
            $table->foreign('institucion_id')->references('id')->on('oinstitucions')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('pestudio_id')->references('id')->on('pestudios')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('grado_id')->references('id')->on('grados')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('tevaluacion_id')->references('id')->on('tevaluacions')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('registro_titulos');
    }
}
