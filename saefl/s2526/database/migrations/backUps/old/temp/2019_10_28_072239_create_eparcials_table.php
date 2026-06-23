<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEparcialsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('eparcials', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('pevaluacion_id')->unsigned()->comment('Plan de Evaluación');
            $table->integer('escala_id')->unsigned()->comment('Escala de Evaluación');
            $table->string('objetivo')->nullable()->comment('Objetivo General');
            $table->string('description')->nullable()->comment('Descripción');

            $table->softDeletes();
            $table->timestamps();

            $table->foreign('pevaluacion_id')->references('id')->on('profesors')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('escala_id')->references('id')->on('profesors')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('eparcials');
    }
}
