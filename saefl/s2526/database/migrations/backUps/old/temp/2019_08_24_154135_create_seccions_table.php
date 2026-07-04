<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSeccionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('seccions', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('grado_id')->unsigned()->comment('Grado del Plan de Estudio');
            $table->string('name',1)->comment('Nombre');
            $table->string('description')->nullable()->comment('Descripción');            
            $table->integer('amount_student')->default(40)->comment('Cantidad de Estudiantes');
            $table->string('observation')->nullable()->comment('Observaciones');
            $table->enum('status_active',['true','false'])->comment('Estado');            
            $table->softDeletes();
            $table->timestamps();
            $table->foreign('grado_id')->references('id')->on('grados')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('seccions');
    }
}
