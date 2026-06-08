<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGradosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('grados', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('pestudio_id')->unsigned()->comment('Plan Estudio');
            $table->string('name')->comment('Nombre');
            $table->string('description')->nullable()->comment('Descripción');
            $table->enum('status_active',['true','false'])->comment('Estado');            
            $table->softDeletes();
            $table->timestamps();
            $table->foreign('pestudio_id')->references('id')->on('pestudios')->onDelete('cascade')->onUpdate('cascade');            
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('grados');
    }
}
