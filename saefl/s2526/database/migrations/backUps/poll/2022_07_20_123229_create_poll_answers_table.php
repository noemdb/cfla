<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePollAnswersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('poll_answers', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->smallinteger('poll_question_id')->unsigned()->comment('Pregunta');
            $table->smallinteger('poll_option_id')->unsigned()->comment('Opción');
            $table->string('token');
            $table->timestamps();
            $table->foreign('poll_question_id')->references('id')->on('poll_questions')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('poll_option_id')->references('id')->on('poll_options')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('poll_answers');
    }
}
