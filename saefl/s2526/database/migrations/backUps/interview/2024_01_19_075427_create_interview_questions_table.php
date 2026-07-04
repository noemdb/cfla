<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateInterviewQuestionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('interview_questions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('interview_id');
            $table->text('text');
            $table->text('observations')->nullable();
            $table->timestamps();

            $table->foreign('interview_id')->references('id')->on('interviews')->onDelete('cascade')->onUpdate('cascade');
        });

        if (!DB::table('interview_questions')->exists()) {
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
            DB::table('interview_questions')->truncate();
            $datas = [
                [ 'interview_id'=>1, 'text' => "1. ¿Qué aspectos positivos destacaría del Colegio Fray Luis Amigó?", 'observations' => "Esta pregunta permite obtener una visión general de los aspectos positivos de nuestro colegio desde la perspectiva de los padres, madres o representantes. Las respuestas a esta pregunta pueden ayudar a nuestro colegio a identificar sus fortalezas y áreas de oportunidad."],
                [ 'interview_id'=>1, 'text' => "2. ¿En qué aspectos ha tenido un impacto positivo la educación de su hijo/a estando en el Colegio Fray Luis Amigó?", 'observations' => "Esta pregunta permite obtener información sobre el impacto positivo de la educación en los estudiantes. Las respuestas a esta pregunta pueden ayudar a nuestro colegio a comprender cómo está contribuyendo al desarrollo de los estudiantes."],
                [ 'interview_id'=>1, 'text' => "3. ¿Qué cambios positivos ha observado en su hijo/a desde que comenzó a asistir al Colegio Fray Luis Amigó?", 'observations' => "Esta pregunta permite obtener información específica sobre los cambios positivos que han experimentado los estudiantes desde que comenzaron a asistir a nuestro colegio. Las respuestas a esta pregunta pueden ayudar a nuestro colegio a comprender cómo está impactando en el desarrollo de los estudiantes."],
                [ 'interview_id'=>1, 'text' => "4. ¿Cuáles son sus recomendaciones para mejorar la experiencia educativa de los estudiantes y las familias en el Colegio Fray Luis Amigó?", 'observations' => "Esta pregunta permite obtener sugerencias de los padres, madres o representantes para mejorar la experiencia de los estudiantes y las familias en nuestro colegio. Las respuestas a esta pregunta pueden ayudar a nuestro colegio a identificar áreas de mejora y a tomar medidas para mejorar la experiencia de todos los miembros de la comunidad educativa."],
            ] ;
            DB::table('interview_questions')->insert($datas);

            /*
            $datas = [
                [ 'interview_id'=>1, 'text' => "1. ¿Qué es lo que más le gusta de nuestra Colegio Fray Luis Amigó? ", 'observations' => "Esta pregunta es abierta y permite a los padres, madres o representantes expresar sus opiniones y sentimientos positivos sobre la institución. Las respuestas pueden ayudar a identificar sus fortalezas y áreas de oportunidad."],
                [ 'interview_id'=>1, 'text' => "2. ¿Qué aspectos de la educación de su hijo/a en nuestra Colegio Fray Luis Amigó han sido más satisfactorios para usted?", 'observations' => "Esta pregunta permite a los padres, madres o representantes centrarse en los aspectos específicos de la educación de sus hijos que han sido positivos. Las respuestas pueden ayudar a nuestro colegio a identificar las áreas en las que está teniendo un impacto positivo en los estudiantes."],
                [ 'interview_id'=>1, 'text' => "3. ¿Qué ha aprendido su hijo/a en nuestra Colegio Fray Luis Amigó que le ha sido especialmente útil o significativo?", 'observations' => "Esta pregunta permite a los padres, madres o representantes reflexionar sobre el aprendizaje de sus hijos. Las respuestas pueden ayudar a identificar los objetivos de aprendizaje que están logrando."],
                [ 'interview_id'=>1, 'text' => "4. ¿Cómo ha cambiado su hijo/a desde que comenzó a asistir a nuestra Colegio Fray Luis Amigó?", 'observations' => "Esta pregunta permite a los padres, madres o representantes evaluar el impacto general de la institución en sus hijos. Las respuestas pueden ayudar a comprender cómo está contribuyendo al desarrollo de los estudiantes."],
                [ 'interview_id'=>1, 'text' => "5. ¿Qué recomendaría a otros padres, madres o representantes sobre nuestra Colegio Fray Luis Amigó?", 'observations' => "Esta pregunta permite a los padres, madres o representantes compartir sus experiencias positivas con otros. Las respuestas pueden ayudar a atraer a nuevos estudiantes y familias a nuestro colegio."],
                [ 'interview_id'=>1, 'text' => "6. ¿Qué sugerencias tiene para mejorar nuestra Colegio Fray Luis Amigó?", 'observations' => "Esta pregunta permite a los padres, madres o representantes compartir sus ideas sobre cómo la institución podría mejorar. Las respuestas pueden ayudar a identificar áreas de mejora y a tomar medidas para mejorar la experiencia de los estudiantes y las familias."],
                [ 'interview_id'=>1, 'text' => "7. ¿Qué desafíos cree usted que enfrentará nuestra Colegio Fray Luis Amigó en el futuro?", 'observations' => "Esta pregunta permite a los padres, madres o representantes reflexionar sobre los desafíos que la institución podría enfrentar en el futuro. Las respuestas pueden ayudar a prepararse para estos desafíos."],
                [ 'interview_id'=>1, 'text' => "8. ¿Qué oportunidades cree usted que tendrá nuestra Colegio Fray Luis Amigó en el futuro?", 'observations' => "Esta pregunta permite a los padres, madres o representantes reflexionar sobre las oportunidades que la institución podría tener en el futuro. Las respuestas pueden ayudar a aprovechar estas oportunidades."],
            ] ;
            */
        } 
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('interview_questions');
    }
}
