<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCampoConocimientosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('campo_conocimientos', function (Blueprint $table) {
            $table->increments('id');
            $table->smallInteger('area_conocimiento_id')->unsigned()->comment('Área de Conocimiento');
            $table->integer('asignatura_id')->unsigned()->comment('Asignatura');
            $table->string('observations')->nullable()->comment('Observaciones');
            $table->timestamps();
            $table->foreign('area_conocimiento_id')->references('id')->on('area_conocimientos')->onDelete('cascade')->onUpdate('cascade');
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
        Schema::dropIfExists('campo_conocimientos');
    }
}
