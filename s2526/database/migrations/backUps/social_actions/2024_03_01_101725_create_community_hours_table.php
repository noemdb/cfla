<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCommunityHoursTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('community_hours', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->comment('Identificador del usuario que registra la hora');
            $table->unsignedBigInteger('community_action_id')->comment('Acción comunitaria');
            $table->unsignedBigInteger('estudiant_id')->comment('Estudiante');
            $table->date('date')->comment('Fecha en la que se realizó la actividad');
            $table->unsignedTinyInteger('duration')->nullable()->comment('Duración (horas)');
            $table->text('observations')->nullable()->comment('Observaciones/Incidencias de la actividad');
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending')->comment('Estado del cumplimiento');
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
        Schema::dropIfExists('community_hours');
    }
}
