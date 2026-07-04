<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCatchmentNotificationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('catchment_notifications', function (Blueprint $table) {
            $table->id();
            $table->integer('catchment_id')->nullable()->unsigned()->comment('Censo');
            $table->integer('interview_id')->nullable()->unsigned()->comment('Entrevista');
            $table->integer('autoridad_id')->nullable()->unsigned()->comment('Autoridad');
            $table->integer('institucion_id')->nullable()->unsigned()->comment('Institución');
            $table->text('subject')->comment('Asunto');
            $table->text('view')->comment('Vista');
            $table->text('list_comment')->comment('modelListComment');
            $table->text('motive')->comment('Motivo');
            $table->string('email')->nullable()->comment('Correo electrónico');
            $table->string('phone',32)->nullable()->comment('Teléfono');
            $table->enum('type', ['email','phone','whatsapp','telegram'])->default('email');
            $table->string('timestamps',16)->comment('Marca de Tiempo');
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
        Schema::dropIfExists('catchment_notifications');
    }
}
