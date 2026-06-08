<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDiagAnswersTable extends Migration
{
    public function up()
    {
        Schema::create('diag_answers', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('estudiant_id')->nullable();
            $table->unsignedBigInteger('question_id')->nullable();
            $table->unsignedBigInteger('session_id')->nullable();
            $table->text('respuesta')->nullable();
            $table->integer('valor_numerico')->nullable();
            $table->timestamp('completado_at')->nullable();
            $table->timestamps();

            $table->foreign('estudiant_id')->references('id')->on('estudiants');
            $table->foreign('question_id')->references('id')->on('diag_questions');
            $table->foreign('session_id')->references('id')->on('diag_sessions');

            $table->index('estudiant_id');
            $table->index('question_id');
            $table->index('session_id');
        });
    }

    public function down()
    {
        Schema::dropIfExists('diag_answers');
    }
}
