<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEiLearningareasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('eilearningareas', function (Blueprint $table) {
            $table->id();
            $table->integer('grado_id')->comment('Grupo de edad: Grupo 1, 2, 3');
            $table->string('name')->comment('Nombre del área de aprendizaje');
            $table->text('description')->comment('Descripción del área aprendizaje');
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
        Schema::dropIfExists('eilearningareas');
    }
}