<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBoletinsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('boletins', function (Blueprint $table) {
            $table->bigIncrements('id');
            // $table->integer('lapso_id')->unsigned()->comment('Lapso');
            $table->bigInteger('estudiant_id')->unsigned()->comment('Estudiante');
            $table->bigInteger('evaluacion_id')->unsigned()->comment('Evaluación');

            $table->string('nota')->nullable()->comment('Nota');

            $table->string('description')->nullable()->comment('Descripción');
            $table->string('observations')->nullable()->comment('Observaciones');

            $table->softDeletes();
            $table->timestamps();

            $table->foreign('estudiant_id')->references('id')->on('estudiants')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('evaluacion_id')->references('id')->on('evaluacions')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('boletins');
    }
}
