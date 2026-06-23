<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEcualitativasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ecualitativas', function (Blueprint $table) {
            $table->increments('id');
            $table->bigInteger('estudiant_id')->unsigned()->comment('Estudiante');
            $table->integer('lapso_id')->unsigned()->nullable()->comment('Lapso');

            $table->string('name')->nullable()->comment('Nombre');
            $table->string('description')->nullable()->comment('Descripción');
            $table->string('observations')->nullable()->comment('Observaciones');

            $table->timestamps();

            $table->foreign('estudiant_id')->references('id')->on('estudiants')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('lapso_id')->references('id')->on('lapsos')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ecualitativas');
    }
}
