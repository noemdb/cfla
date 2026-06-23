<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePollTokensTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('poll_tokens', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->smallinteger('poll_main_id')->unsigned()->comment('Encuesta');
            $table->bigInteger('representant_id')->unsigned()->nullable()->comment('Representate');
            $table->bigInteger('estudiant_id')->unsigned()->nullable()->comment('Estudiante');
            $table->string('email');
            $table->string('token')->unique();
            $table->enum('status_notified',['true','false'])->default('false')->comment('Notificaciones enviadas');
            $table->enum('status_notifiled',['true','false'])->default('false')->comment('Estado de la notificación');

            $table->timestamps();
            $table->foreign('poll_main_id')->references('id')->on('poll_mains')->onDelete('cascade')->onUpdate('cascade');
            // $table->foreign('representant_id')->references('id')->on('representants')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('poll_tokens');
    }
}
