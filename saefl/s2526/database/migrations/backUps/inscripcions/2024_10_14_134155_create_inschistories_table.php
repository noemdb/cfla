<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInschistoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('inschistories', function (Blueprint $table) {
            $table->id('id');
            $table->unsignedInteger('seccion_id')->comment('Sección a inscribir');
            $table->unsignedBigInteger('estudiant_id')->comment('Estudiante');
            $table->string('pescolar')->nullable()->comment('Período Escolar');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('inschistories');
    }
}
