<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateIncidentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('incidents', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('estudiant_id')->unsigned()->comment('Estudiante');
            $table->integer('profesor_id')->unsigned()->comment('Profesor');
            $table->smallInteger('reason_id')->unsigned()->comment('Motivo');
            $table->enum('type',['Académica','Disciplinaria','Otro'])->comment('Tipo');
            $table->string('description')->comment('Descripción');
            $table->string('observations')->nullable()->comment('Observaciones');
            $table->string('taken_actions')->comment('Acciones tomadas');
            $table->boolean('status_aggression')->nullable()->comment('Presento agresividad');
            $table->boolean('status_notify')->nullable()->comment('Notificada');
            $table->boolean('status_notify_agreement')->nullable()->comment('Notificación de acuerdo');
            $table->boolean('status_announcement')->nullable()->comment('Convocatoria');
            $table->boolean('status_active')->nullable()->comment('Estado');
            $table->timestamps();
            $table->foreign('estudiant_id')->references('id')->on('estudiants')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('profesor_id')->references('id')->on('profesors')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('reason_id')->references('id')->on('incident_reasons')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('incidents');
    }
}

/*

DROP TABLE ` incidents `

*/