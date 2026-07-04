<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDiagOptionsTable extends Migration
{
    public function up()
    {
        Schema::create('diag_options', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('question_id');
            $table->text('opcion');
            $table->integer('valor')->default(0);
            $table->integer('orden')->default(0);
            $table->timestamps();

            $table->foreign('question_id')
                  ->references('id')->on('diag_questions')
                  ->onDelete('cascade');

            $table->index('question_id');
        });
    }

    public function down()
    {
        Schema::dropIfExists('diag_options');
    }
}
