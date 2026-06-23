<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreatePollOptionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('poll_options', function (Blueprint $table) {
            $table->smallIncrements('id');
            $table->smallinteger('poll_question_id')->unsigned()->comment('Pregunta');
            $table->string('text');
            $table->string('description')->nullable()->comment('Descripción de la encuesta');
            $table->string('observations')->nullable()->comment('Observaciones de la encuesta');
            $table->string('image')->nullable()->comment('Imagen');
            $table->text('body')->nullable();
            $table->timestamps();
            $table->foreign('poll_question_id')->references('id')->on('poll_questions')->onDelete('cascade')->onUpdate('cascade');
        });

        DB::table("poll_options")
        ->insert([
            "poll_question_id" => 1,
            "text" => "Candidata N1",
            "description" => "Candidata 1ER Grado",
            "observations" => "Candidata 1ER Grado",
            "image" => "images/avatar/poll/1.png",
            "body" => "
                Lo que le gusta:
                <ul>
                    <li>Cantar</li>
                    <li>Bailar</li>
                    <li>Jugar Tenis de mesa</li>
                    <li>Revisar sus redes sociales</li>
                </ul>

                Lo que le no gusta:
                <ul>
                    <li>Ruidos fuertes</li>
                    <li>El calor</li>
                    <li>La espinaca</li>
                </ul>
            ",
        ]);

        DB::table("poll_options")
        ->insert([
            "poll_question_id" => 1,
            "text" => "Candidata N2",
            "description" => "Candidata 2DO Grado",
            "observations" => "Candidata 2DO Grado",
            "image" => "images/avatar/poll/2.png",
            "body" => "
                Lo que le gusta:
                <ul>
                    <li>Actuar</li>
                    <li>Grabar videos</li>
                    <li>Comer cereal</li>
                    <li>Revisar sus redes sociales</li>
                </ul>

                Lo que le no gusta:
                <ul>
                    <li>Sudar</li>
                    <li>Madrugar</li>
                    <li>Lechosa</li>
                </ul>
            ",
        ]);

        DB::table("poll_options")
        ->insert([
            "poll_question_id" => 1,
            "text" => "Candidata N3",
            "description" => "Candidata 3ER Grado",
            "observations" => "Candidata 3ER Grado",
            "image" => "images/avatar/poll/3.png",
            "body" => "
                Lo que le gusta:
                <ul>
                    <li>Bailar</li>
                    <li>Cantar</li>
                    <li>Jugar basketbol</li>
                    <li>Revisar sus redes sociales</li>
                </ul>

                Lo que le no gusta:
                <ul>
                    <li>El calor</li>
                    <li>Ruidos fuertes</li>
                    <li>La patilla</li>
                </ul>
            ",
        ]);

        //////////////////////////////////////////////////////////////
        DB::table("poll_options")
        ->insert([
            "poll_question_id" => 2,
            "text" => "Candidata N1",
            "description" => "Candidata 1ER Grado",
            "observations" => "Candidata 1ER Grado",
            "image" => "images/avatar/poll/1.png",
            "body" => "
                Lo que le gusta:
                <ul>
                    <li>Cantar</li>
                    <li>Bailar</li>
                    <li>Jugar Tenis de mesa</li>
                    <li>Revisar sus redes sociales</li>
                </ul>

                Lo que le no gusta:
                <ul>
                    <li>Ruidos fuertes</li>
                    <li>El calor</li>
                    <li>La espinaca</li>
                </ul>
            ",
        ]);

        DB::table("poll_options")
        ->insert([
            "poll_question_id" => 2,
            "text" => "Candidata N2",
            "description" => "Candidata 2DO Grado",
            "observations" => "Candidata 2DO Grado",
            "image" => "images/avatar/poll/2.png",
            "body" => "
                Lo que le gusta:
                <ul>
                    <li>Actuar</li>
                    <li>Grabar videos</li>
                    <li>Comer cereal</li>
                    <li>Revisar sus redes sociales</li>
                </ul>

                Lo que le no gusta:
                <ul>
                    <li>Sudar</li>
                    <li>Madrugar</li>
                    <li>Lechosa</li>
                </ul>
            ",
        ]);

        DB::table("poll_options")
        ->insert([
            "poll_question_id" => 2,
            "text" => "Candidata N3",
            "description" => "Candidata 3ER Grado",
            "observations" => "Candidata 3ER Grado",
            "image" => "images/avatar/poll/3.png",
            "body" => "
                Lo que le gusta:
                <ul>
                    <li>Bailar</li>
                    <li>Cantar</li>
                    <li>Jugar basketbol</li>
                    <li>Revisar sus redes sociales</li>
                </ul>

                Lo que le no gusta:
                <ul>
                    <li>El calor</li>
                    <li>Ruidos fuertes</li>
                    <li>La patilla</li>
                </ul>
            ",
        ]);

        ////////////////////////////////////////////////////////
        DB::table("poll_options")
        ->insert([
            "poll_question_id" => 3,
            "text" => "Candidata N1",
            "description" => "Candidata 1ER Grado",
            "observations" => "Candidata 1ER Grado",
            "image" => "images/avatar/poll/1.png",
            "body" => "
                Lo que le gusta:
                <ul>
                    <li>Cantar</li>
                    <li>Bailar</li>
                    <li>Jugar Tenis de mesa</li>
                    <li>Revisar sus redes sociales</li>
                </ul>

                Lo que le no gusta:
                <ul>
                    <li>Ruidos fuertes</li>
                    <li>El calor</li>
                    <li>La espinaca</li>
                </ul>
            ",
        ]);

        DB::table("poll_options")
        ->insert([
            "poll_question_id" => 3,
            "text" => "Candidata N2",
            "description" => "Candidata 2DO Grado",
            "observations" => "Candidata 2DO Grado",
            "image" => "images/avatar/poll/2.png",
            "body" => "
                Lo que le gusta:
                <ul>
                    <li>Actuar</li>
                    <li>Grabar videos</li>
                    <li>Comer cereal</li>
                    <li>Revisar sus redes sociales</li>
                </ul>

                Lo que le no gusta:
                <ul>
                    <li>Sudar</li>
                    <li>Madrugar</li>
                    <li>Lechosa</li>
                </ul>
            ",
        ]);

        DB::table("poll_options")
        ->insert([
            "poll_question_id" => 3,
            "text" => "Candidata N3",
            "description" => "Candidata 3ER Grado",
            "observations" => "Candidata 3ER Grado",
            "image" => "images/avatar/poll/3.png",
            "body" => "
                Lo que le gusta:
                <ul>
                    <li>Bailar</li>
                    <li>Cantar</li>
                    <li>Jugar basketbol</li>
                    <li>Revisar sus redes sociales</li>
                </ul>

                Lo que le no gusta:
                <ul>
                    <li>El calor</li>
                    <li>Ruidos fuertes</li>
                    <li>La patilla</li>
                </ul>
            ",
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('poll_options');
    }
}
