<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePasesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pases', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id')->unsigned()->comment('Usuario');
            $table->integer('estudiant_id')->unsigned()->comment('Estudiante');
            $table->integer('profesor_id')->nullable()->unsigned()->comment('Profesor');
            $table->integer('pensum_id')->nullable()->unsigned()->comment('Área de Formación');
            $table->string('type')->default('Entrada')->comment('Tipo'); //Salida,Entrada,Especial,Temporal,Uso de Medicamentos
            $table->string('motive')->default('Personal')->comment('Motivo'); // Personal, Médico, Familiar, Extracurricular
            $table->string('description')->comment('Descripción');
            $table->string('destination')->nullable()->comment('Destino');
            $table->date('date')->comment('Fecha');
            $table->time('time')->comment('Hora');
            $table->integer('duration')->nullable()->comment('Duración');
            $table->string('status')->default('Pendiente')->comment('Estado');
            $table->string('code_verification')->nullable()->unique()->comment('Código de Verificación');
            $table->boolean('require_auhtorize_guardian')->nullable()->default(false)->comment('Requiere autorización del representante?');
            $table->boolean('require_auhtorize_teacher')->nullable()->default(false)->comment('Requiere autorización del Profesor?');
            $table->boolean('require_auhtorize_manager')->nullable()->default(false)->comment('Requiere autorización del Coordinador?');
            $table->boolean('status_emergency')->nullable()->default(false)->comment('Es una emergencia?');
            $table->boolean('status_notifications')->nullable()->default(false)->comment('Notificación enviada');
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
        Schema::dropIfExists('pases');
    }
}
