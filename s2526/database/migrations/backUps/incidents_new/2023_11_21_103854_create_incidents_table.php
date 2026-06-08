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
            $table->increments('id');
            $table->integer('user_id')->unsigned()->nullable();
            $table->BigInteger('estudiant_id')->unsigned();
            $table->integer('profesor_id')->unsigned();
            $table->integer('duty_id')->unsigned();
            $table->integer('fault_id')->unsigned();

            $table->text('description')->comment('Descripción.');
            $table->text('observations')->nullable()->comment('Observaciones');
            $table->boolean ('status_notify_close')->default(false)->comment('Notificación de Incidente cerrado');
            $table->string('close_observations')->nullable()->comment('Observaciòn de cierre');
            $table->boolean('status_pedagogical')->nullable()->default(false)->comment('Correctivo pedagógico');
            $table->boolean('status_close')->default(false)->comment('Incidente cerrado');
            $table->text('taken_actions')->nullable()->comment('Acciones tomadas');
            $table->boolean('status_reiterative')->nullable()->default(false)->comment('Notificación de Incidente cerrado');
            $table->boolean('status_notify')->default(false)->comment('Notificada');
            $table->timestamp('date_notify_email')->nullable()->comment('Fecha de notificación');
            $table->boolean('status_notify_agreement')->default(false)->comment('Notificación de acuerdo');
            $table->timestamp('date_notify_agreement_email')->nullable()->comment('Fecha de notificación del acuerdo');
            $table->boolean('status_announcement')->nullable()->default(false)->comment('Convocatoria');
            $table->date('date_announcement')->nullable()->comment('Fecha Programada');
            $table->time('hour_announcement')->nullable()->comment('Hora Programada');
            $table->boolean('status_active')->default(true)->comment('Estado');
            $table->timestamp('date_close')->nullable()->comment('Fecha de notificación');
            
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('estudiant_id')->references('id')->on('estudiants')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('profesor_id')->references('id')->on('profesors')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('duty_id')->references('id')->on('incident_duties')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('fault_id')->references('id')->on('incident_faults')->onDelete('cascade')->onUpdate('cascade');
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
