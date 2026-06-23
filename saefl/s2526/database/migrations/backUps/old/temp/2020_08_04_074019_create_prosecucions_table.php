<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProsecucionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('prosecucions', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('seccion_id')->unsigned()->comment('Sección a inscribir');
            $table->bigInteger('estudiant_id')->unique()->unsigned()->comment('Estudiante');
            $table->string('observations')->nullable()->comment('Observaciones');
            $table->timestamps();
            $table->foreign('seccion_id')->references('id')->on('seccions')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('estudiant_id')->references('id')->on('estudiants')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('prosecucions');
    }
}
