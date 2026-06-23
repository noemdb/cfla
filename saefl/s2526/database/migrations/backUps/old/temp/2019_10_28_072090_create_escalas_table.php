<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEscalasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('escalas', function (Blueprint $table) {
            $table->increments('id');
            // $table->integer('tescala_id')->unsigned()->comment('Tipo de Escala');
            $table->string('name')->comment('Nombre');            
            $table->string('minimo')->comment('Calificación mínima');
            $table->string('maxima')->comment('Calificación máxima');
            $table->string('aprobacion')->comment('Calificación mínima aprobatoria');
            $table->timestamps();
            // $table->foreign('tescala_id')->references('id')->on('tescalas')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('escalas');
    }
}
