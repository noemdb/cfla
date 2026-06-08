<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProfesorsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('profesors', function (Blueprint $table) {
            $table->increments('id');
            $table->string('ti_teacher')->comment('Tipo de facilitador');
            $table->string('ci_profesor')->comment('Cédula de identidad, Id temporal o pasaporte');
            $table->string('lastname')->nullable()->comment('Nombres');
            $table->string('name')->nullable()->comment('Nombres');
            $table->string('gender')->nullable()->comment('Genero');//Másculino,Femenino
            $table->date('date_birth')->nullable()->comment('Fecha de nacimiento');
            $table->string('city_birth')->nullable()->comment('Lugar de nacimiento');
            $table->string('dir_address')->nullable()->comment('Dirección de residencia');
            $table->string('phone')->nullable()->comment('Número de teléfono fijo');
            $table->string('cellphone')->nullable()->comment('Número de teléfono celular');
            $table->string('email')->nullable()->comment('Correo electrónico');
            $table->enum('status_active',['true','false'])->comment('Estado del Banco');

            $table->softDeletes();
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
        Schema::dropIfExists('profesors');
    }
}
