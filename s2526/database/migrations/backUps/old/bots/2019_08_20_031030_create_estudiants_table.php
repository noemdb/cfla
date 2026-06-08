<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEstudiantsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('estudiants', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('planpago_id')->unsigned();
            $table->integer('grado_inicial_id')->default(1)->nullable()->unsigned()->comment('Grado de entrada en la institución');
            $table->string('seccion_inicial',1)->default(1)->nullable()->comment('Secciónn de entrada en la institución');
            //$table->string('photo_profile')->nullable()->comment('Foto del perfil');
            $table->integer('type_ci_id')->unsigned()->default(1)->comment('Tipo de identificación');
            $table->string('ci_estudiant')->unique()->comment('Cédula de identidad');
            $table->string('ci_estudiant_temp')->nullable()->comment('Cédula Escolar o Identificador Temporal');
            $table->string('lastname')->nullable()->comment('Apellidos');
            $table->string('name')->nullable()->comment('Nombres');
            $table->enum('gender',['Masculino', 'Femenino'])->nullable()->comment('Genero');//Másculino,Femenino
            $table->date('date_birth')->nullable()->comment('Fecha de nacimiento');
            $table->string('city_birth')->nullable()->comment('Ciudad de nacimiento');
            $table->string('town_hall_birth')->nullable()->comment('Municipio de nacimiento');
            $table->string('state_birth')->nullable()->comment('Estado de nacimiento');
            $table->string('country_birth')->nullable()->comment('País de nacimiento');
            $table->string('dir_address')->nullable()->comment('Dirección de residencia');
            $table->string('phone')->nullable()->comment('Número de teléfono fijo');
            $table->string('cellphone')->nullable()->comment('Número de teléfono celular');
            $table->string('email')->nullable()->comment('Correo electrónico');           
            // $table->enum('status_nacionality',['true','false'])->nullable()->comment('Nacionalizado');

            $table->string('representant_ci')->default('1111111111')->comment('Cédula de identidad, Id temporal o pasaporte Representate');
            $table->bigInteger('representant_id')->unsigned()->default('1111111111')->comment('Cédula de identidad, Id temporal o pasaporte Representate');
            
            // $table->string('n_pasaporte')->nullable()->comment('Número de Pasaporte');
            // $table->string('n_carta_consular')->nullable()->comment('Identificador Consular');            
            
            $table->enum('status_active',['true','false'])->default('true')->comment('Estado');            

            $table->foreign('representant_id')->references('id')->on('representants')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('planpago_id')->references('id')->on('planpagos')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('type_ci_id')->references('id')->on('type_cis')->onDelete('cascade')->onUpdate('cascade');

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
        Schema::dropIfExists('estudiants');
    }
}
