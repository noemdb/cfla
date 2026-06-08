<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreatePollQuestionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('poll_questions', function (Blueprint $table) {
            $table->smallIncrements('id');
            $table->smallinteger('poll_main_id')->unsigned()->comment('Encuesta');
            $table->string('text');
            $table->string('description')->nullable()->comment('Descripción de la encuesta');
            $table->string('observations')->nullable()->comment('Observaciones de la encuesta');
            $table->string('image')->nullable()->comment('Imagen');
            $table->text('body')->nullable();
            $table->timestamps();
            $table->foreign('poll_main_id')->references('id')->on('poll_mains')->onDelete('cascade')->onUpdate('cascade');
        });

        DB::table("poll_questions")
        ->insert([
            "poll_main_id" => 1,
            "text" => "¿Cúal de las siguientes candidatas prefiere como Reina del CFLA 2023?",
            "description" => "Reina del CFLA 2023 - Semana aniversario",
            "observations" => "Reina del CFLA 2023 - Semana aniversario",
        ]);

        DB::table("poll_questions")
        ->insert([
            "poll_main_id" => 1,
            "text" => "¿Cúal de las siguientes candidatas prefiere como Reina del Internet CFLA 2023?",
            "description" => "Reina del Internet CFLA 2023 - Semana aniversario",
            "observations" => "Reina del Internet CFLA 2023 - Semana aniversario",
        ]);

        DB::table("poll_questions")
        ->insert([
            "poll_main_id" => 1,
            "text" => "¿Cúal de las siguientes candidatas prefiere como Reina de la amistad CFLA 2023?",
            "description" => "Reina de la amistad CFLA 2023 - Semana aniversario",
            "observations" => "Reina de la amistad CFLA 2023 - Semana aniversario",
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('poll_questions');
    }
}
