<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePeducativosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //Especialidades
        Schema::create('peducativos', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('pescolar_id')->unsigned()->comment('Príodo Escolar');
            // $table->string('code')->comment('Codigo');
            $table->string('name')->comment('Nombre');
            $table->string('description')->comment('Descripción');
            $table->integer('order')->nullable()->comment('Orden de presentación');
            $table->enum('status_active',['true','false'])->comment('Estado');            
            $table->softDeletes();
            $table->timestamps();
            $table->foreign('pescolar_id')->references('id')->on('pescolars')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('peducativos');
    }
}
