<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMailersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mailers', function (Blueprint $table) {
            $table->id();
            $table->string('name')->comment('Nombre');
            $table->string('code',10)->unique()->comment('Código');
            $table->integer('user_id')->unsigned();
            $table->integer('seccion_id')->unsigned()->nullable()->comment('Sección a inscribir');
            $table->integer('grado_id')->unsigned()->nullable()->comment('Grado del Plan de Estudio');
            $table->string('description')->nullable()->comment('Descripción');
            $table->timestamp('date')->comment('Fecha Programada');
            $table->date('fecha')->comment('Fecha Programada');
            $table->time('time')->comment('Hora Programada');
            
            $table->text('subject')->comment('asunto');
            $table->text('title')->comment('Título');
            $table->text('subtitle')->nullable()->comment('Subtítulo');
            $table->text('greeting')->nullable()->comment('Saludo formal');
            $table->longText('body')->comment('cuerpo del mensaje');
            $table->text('insert')->nullable()->comment('Insertado');
            $table->text('footer')->nullable()->comment('Despedida');
            $table->enum('status',['true','false'])->default('true')->comment('Estado de aprobación');
            $table->enum('status_adviders',['true','false'])->default('false')->comment('Estado de Asesores');

            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('grado_id')->references('id')->on('grados')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('seccion_id')->references('id')->on('seccions')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('mailers');
    }
}
