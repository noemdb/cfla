<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePevaluacionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pevaluacions', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('profesor_id')->unsigned()->comment('Profesor');
            $table->bigInteger('pensum_id')->unsigned()->comment('Asignatura');
            $table->integer('lapso_id')->unsigned()->comment('Lapso');
            $table->string('objetivo')->nullable()->comment('Objetivo General');
            $table->string('description')->nullable()->comment('Descripción');
            $table->string('observations')->nullable()->comment('Observaciones');

            $table->softDeletes();
            $table->timestamps();

            $table->foreign('profesor_id')->references('id')->on('profesors')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('pensum_id')->references('id')->on('pensums')->onDelete('cascade')->onUpdate('cascade');
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
        Schema::dropIfExists('pevaluacions');
    }
}
