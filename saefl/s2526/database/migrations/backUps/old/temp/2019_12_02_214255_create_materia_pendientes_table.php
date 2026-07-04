<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMateriaPendientesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('materia_pendientes', function (Blueprint $table) {
            $table->Increments('id');
            $table->bigInteger('pensum_id')->unsigned()->comment('Asignatura');
            $table->bigInteger('estudiant_id')->unique()->unsigned()->comment('Estudiante');
            $table->string('observations')->nullable()->comment('Observaciones');
            $table->softDeletes();
            $table->timestamps();
            $table->foreign('pensum_id')->references('id')->on('pensums')->onDelete('cascade')->onUpdate('cascade');
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
        Schema::dropIfExists('materia_pendientes');
    }
}
