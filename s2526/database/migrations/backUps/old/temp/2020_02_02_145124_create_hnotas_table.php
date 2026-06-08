<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateHnotasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('hnotas', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('pensum_id')->unsigned()->comment('Pensums');
            $table->integer('grupo_estable_id')->nullable()->unsigned()->comment('Sub grupo de la Asignatura');
            $table->smallinteger('tevaluacion_id')->nullable()->unsigned()->comment('Tipo de Evaluación');
            $table->bigInteger('estudiant_id')->unsigned()->comment('Estudiante');
            $table->bigInteger('historico_nota_id')->unsigned()->comment('historico notas');
            $table->smallinteger('institucion_id')->unsigned();
            $table->float('valor',5,2)->nullable()->comment('Calificación Numérica');
            $table->string('literal',20)->nullable()->comment('Calificación Cualitativa');
            $table->enum('tipo',['F','R','P','O'])->default('F')->comment('Tipo de Evaluación - old');
            $table->date('fecha')->nullable()->comment('Fecha - Mes-Año');
            $table->string('description')->nullable()->comment('Descripción');
            $table->string('observations')->nullable()->comment('Observaciones');
            $table->integer('user_id')->unsigned();
            $table->softDeletes();
            $table->timestamps();
            $table->foreign('pensum_id')->references('id')->on('pensums')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('grupo_estable_id')->references('id')->on('grupo_estables')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('tevaluacion_id')->references('id')->on('tevaluacions')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('estudiant_id')->references('id')->on('estudiants')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('historico_nota_id')->references('id')->on('historico_notas')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('institucion_id')->references('id')->on('oinstitucions')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('hnotas');
    }
}
