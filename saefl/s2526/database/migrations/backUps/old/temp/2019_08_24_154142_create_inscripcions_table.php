<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateInscripcionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('inscripcions', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('tipo_id')->unsigned()->comment('Tipo de Inscripción');
            $table->integer('seccion_id')->unsigned()->comment('Sección a inscribir');
            $table->bigInteger('estudiant_id')->unique()->unsigned()->comment('Estudiante');
            $table->integer('escolaridad_id')->default('1')->unsigned()->comment('Estudiante');
            $table->integer('programacion_id')->nullable()->unsigned()->comment('Estudiante');
            $table->integer('grupo_estable_id')->nullable()->unsigned()->comment('Grupo Estable');
            $table->string('observations')->nullable()->comment('Observaciones');
            $table->softDeletes();
            $table->timestamps();
            $table->foreign('seccion_id')->references('id')->on('seccions')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('estudiant_id')->references('id')->on('estudiants')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('tipo_id')->references('id')->on('tinscripcions')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('escolaridad_id')->references('id')->on('escolaridads')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('programacion_id')->references('id')->on('programacions')->onDelete('cascade')->onUpdate('cascade');
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
        Schema::dropIfExists('inscripcions');
    }
}
