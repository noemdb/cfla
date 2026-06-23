<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDiagQuestionsTable extends Migration
{
    public function up()
    {
        Schema::create('diag_questions', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('pensum_id')->nullable();
            $table->text('pregunta');
            $table->enum('tipo_pregunta', ['multiple', 'open', 'scale']);
            $table->integer('orden')->default(0);
            $table->integer('weighing')->default(0);
            $table->enum('difficulty', ['easy','medium','hard']);
            $table->boolean('activo')->default(true);
            $table->timestamps();

            $table->foreign('pensum_id')->references('id')->on('pensums');

            $table->index('pensum_id');
        });
    }

    public function down()
    {
        Schema::dropIfExists('diag_questions');
    }
}
