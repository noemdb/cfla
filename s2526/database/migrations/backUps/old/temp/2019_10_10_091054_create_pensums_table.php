<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePensumsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pensums', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('pestudio_id')->unsigned()->comment('Plan Estudio');
            $table->integer('grado_id')->unsigned()->comment('Grado del Plan de Estudio');
            $table->integer('asignatura_id')->unsigned()->comment('Asignatura');
            $table->integer('escolaridad_id')->default('1')->unsigned()->comment('Escolaridad');
            $table->softDeletes();
            $table->timestamps();
            $table->foreign('pestudio_id')->references('id')->on('pestudios')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('grado_id')->references('id')->on('grados')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('asignatura_id')->references('id')->on('asignaturas')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('escolaridad_id')->references('id')->on('escolaridads')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pensums');
    }
}
