<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTitulosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('titulos', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('registro_titulo_id')->unsigned();
            $table->bigInteger('estudiant_id')->unsigned()->comment('Estudiante');
            $table->integer('seccion_id')->unsigned()->comment('Sección');

            $table->string('serie')->comment('Serial del Título');
            $table->string('observations')->nullable()->comment('Observaciones');

            $table->softDeletes();
            $table->timestamps();

            $table->foreign('registro_titulo_id')->references('id')->on('registro_titulos')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('estudiant_id')->references('id')->on('estudiants')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('seccion_id')->references('id')->on('seccions')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('titulos');
    }
}
