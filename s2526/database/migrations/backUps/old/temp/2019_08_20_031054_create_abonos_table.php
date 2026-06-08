<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAbonosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('abonos', function (Blueprint $table) {
            $table->increments('id');
            $table->BigInteger('representant_id')->unsigned()->comment('Estudiante');
            $table->BigInteger('estudiant_id')->unsigned()->comment('Estudiante');
            $table->BigInteger('ingreso_id')->unsigned()->comment('ingreso');
            $table->string('abono_description')->nullable()->comment('Descripción Abono');
            $table->softDeletes();
            $table->timestamps();
            $table->foreign('representant_id')->references('id')->on('representants')->onDelete('cascade')->onUpdate('cascade');
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
        Schema::dropIfExists('abonos');
    }
}
