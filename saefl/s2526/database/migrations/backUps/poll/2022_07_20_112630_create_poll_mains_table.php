<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreatePollMainsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('poll_mains', function (Blueprint $table) {
            $table->smallIncrements('id');
            $table->smallinteger('poll_group_id')->unsigned()->comment('Grupo de la encuesta');
            $table->integer('autoridad_id')->unsigned()->nullable();
            $table->integer('user_id')->unsigned()->nullable();
            $table->string('name')->comment('Nombre de la encuesta');
            $table->string('description')->comment('Descripción de la encuesta');
            $table->string('observations')->nullable()->comment('Observaciones de la encuesta');
            $table->string('image')->nullable()->comment('Imagen');

            $table->date('date_start');
            $table->date('date_end');

            $table->time('time_start');
            $table->time('time_end');

            $table->enum('status_notified',['true','false'])->default('false')->comment('Notificaciones enviadas');
            $table->enum('status_test',['true','false'])->default('false')->comment('Consulta de Prueba');
            $table->enum('status_notifiled',['true','false'])->default('false')->comment('Estado de la notificación');
            $table->enum('status_representant',['true','false'])->default('false')->comment('Dirigido a representantes');
            $table->enum('status_estudiant',['true','false'])->default('false')->comment('Dirigido a estudiantes');

            $table->foreign('poll_group_id')->references('id')->on('poll_groups')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('autoridad_id')->references('id')->on('autoridads')->onDelete('cascade')->onUpdate('cascade');
            $table->timestamps();
        });

        DB::table("poll_mains")
        ->insert([
            "poll_group_id" => 1,
            "autoridad_id" => 1,
            "user_id" => 1,
            "name" => "Elección de las Novias del CFLA 2023",
            "description" => "Elección de las Novias del CFLA 2023",
            "observations" => "Elección de las Novias del CFLA 2023",
            // "image" => "images/avatar/poll/bg-1.png",
            "date_start" => "2023-04-01",
            "time_start" => "06:00:00",
            "date_end" => "2023-04-07",
            "time_end" => "12:00:00",
            "status_estudiant" => "true",
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('poll_mains');
    }
}
