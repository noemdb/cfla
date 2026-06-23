<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAutoridadsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //Autoridades de la institución
        Schema::create('autoridads', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('tipo_id')->unsigned()->comment('Tipo Autoridad');
            $table->integer('pescolar_id')->unsigned()->comment('Perríodo Escolar');
            $table->integer('institucion_id')->unsigned()->comment('Institución');            
            $table->string('name')->comment('Nombres');
            $table->string('lastname')->comment('Apellidos');
            $table->string('ci')->comment('Cédula de identidad');
            $table->string('position')->comment('Cargo Autoridad');
            $table->string('profile_professional')->nullable()->comment('Perfil profesional');
            $table->string('photo')->nullable()->comment('Imagen');
            $table->date('finicial')->nullable();
            $table->date('ffinal')->nullable();
            $table->timestamps();
            $table->foreign('tipo_id')->references('id')->on('tautoridads')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('pescolar_id')->references('id')->on('pescolars')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('institucion_id')->references('id')->on('institucions')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('autoridads');
    }
}
