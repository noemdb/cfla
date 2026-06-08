<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePestudiosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pestudios', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('peducativo_id')->unsigned()->comment('Plan Educativo');
            $table->string('code')->comment('Código');
            $table->string('name')->comment('Nombre');
            $table->integer('order')->comment('Orden de presentación');
            $table->string('description')->comment('Descripción');
            $table->string('description_aux')->nullable()->comment('Descripción auxiliar');
            $table->string('mention')->comment('Mención');
            $table->enum('status_build_promotion',['true','false'])->comment('Genera promoción');
            $table->string('title')->comment('Descripción completa del titulo que se otorga');
            $table->integer('scale')->comment('Escala de evaluación');
            $table->text('profile')->nullable()->comment('Perfil');
            $table->enum('show_hr',['true','false'])->comment('Mostrar en cuadro de honor');
            $table->enum('status_a_cualitative',['true','false'])->comment('Asociado a equivalencias de calificaciones cualitativas');
            $table->enum('status_active',['true','false'])->comment('Estado');
            $table->enum('status_baremo',['true','false'])->comment('Nota final literal');
            $table->enum('paper',['oficial','letter'])->default('letter')->comment('Estado');
            $table->softDeletes();
            $table->timestamps();
            $table->foreign('peducativo_id')->references('id')->on('peducativos')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pestudios');
    }
}
