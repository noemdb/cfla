<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateHpensumsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('hpensums', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('pescolar_id')->unsigned()->comment('Periodo Escolar');
            $table->integer('pestudio_id')->unsigned()->comment('Plan Estudio');
            $table->integer('grado_id')->unsigned()->comment('Grado del Plan de Estudio');
            $table->integer('asignatura_id')->unsigned()->comment('Asignatura');
            $table->softDeletes();
            $table->timestamps();
            $table->foreign('pescolar_id')->references('id')->on('pescolars')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('pestudio_id')->references('id')->on('pestudios')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('grado_id')->references('id')->on('grados')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('asignatura_id')->references('id')->on('asignaturas')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('hpensums');
    }
}
