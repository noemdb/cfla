<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateActivitiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('activities', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('pevaluacion_id')->unsigned()->comment('Plan de Evaluación');
            $table->date('finicial')->comment('Fecha Inicial');
            $table->date('ffinal')->comment('Fecha Final');
            $table->text('topic')->nullable()->comment('Tema');
            $table->text('thematic')->nullable()->comment('Tejido temático');
            $table->text('references')->comment('Referentes teórico práticos');
            $table->text('teaching')->comment('Enseñanza');
            $table->text('learning')->comment('Aprendizaje');
            $table->text('description')->nullable()->comment('Descripción');
            $table->text('observations')->comment('Observaciones');
            $table->text('comments')->nullable()->comment('Comentarios');
            $table->boolean('status')->default(false)->comment('Aprobación');
            $table->timestamps();
            
            $table->foreign('pevaluacion_id')->references('id')->on('pevaluacions')->onDelete('cascade')->onUpdate('cascade');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('activities');
    }
}
