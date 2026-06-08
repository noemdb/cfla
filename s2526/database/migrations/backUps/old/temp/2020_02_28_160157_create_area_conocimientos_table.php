<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAreaConocimientosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('area_conocimientos', function (Blueprint $table) {
            $table->smallIncrements('id');
            $table->integer('pestudio_id')->unsigned()->comment('Plan Estudio');
            $table->string('name')->comment('Nombre');
            $table->string('code')->nullable()->comment('Código');
            $table->string('code_sm')->nullable()->comment('Abreviatura');
            $table->string('description')->nullable()->comment('Descripción');
            $table->string('observations')->nullable()->comment('Observaciones');
            $table->integer('order')->comment('Número de orden de presentaci+n de la asignatura');
            $table->enum('enable_academic_index',['true','false'])->nullable()->comment('Tomada en cuenta para índice o promedio académico');
            $table->softDeletes();
            $table->timestamps();
            $table->foreign('pestudio_id')->references('id')->on('pestudios')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('area_conocimientos');
    }
}
