<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBoletinAjustesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('boletin_ajustes', function (Blueprint $table) {
            $table->increments('id');
            $table->bigInteger('pevaluacion_id')->unsigned()->comment('Contenido');
            $table->bigInteger('estudiant_id')->unsigned()->comment('Estudiante');
            $table->integer('ajuste')->nullable()->comment('Ajuste');
            $table->string('description')->nullable()->comment('Descripción');
            $table->integer('user_id')->unsigned();
            $table->timestamps();
            $table->foreign('pevaluacion_id')->references('id')->on('pevaluacions')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('estudiant_id')->references('id')->on('estudiants')->onDelete('cascade')->onUpdate('cascade');
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
        Schema::dropIfExists('boletin_ajustes');
    }
}
