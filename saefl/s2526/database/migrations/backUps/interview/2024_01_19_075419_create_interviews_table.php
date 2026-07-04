<?php

use Carbon\Carbon;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateInterviewsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('interviews', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id')->unsigned();
            $table->string('name', 255);
            $table->text('description');
            $table->boolean('status')->default(true);
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade')->onUpdate('cascade');
        });

        if (!DB::table('interviews')->exists()) {
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
            DB::table('interviews')->truncate();
            $datas = [
                [ 'name' => "Perfil de representante del CFLA: experiencia, opiniones y perspectivas", 'description' => "Esta entrevista tiene como objetivo conocer la experiencia positiva de los padres, madres o representantes de los estudiantes del Colegio Fray Luis Amigó. Las preguntas están diseñadas para permitirles expresar sus opiniones y sentimientos sobre la institución, su educación y el desarrollo de sus hijos.", 'status' => true, 'created_at'=>Carbon::now()],
            ] ;
            DB::table('interviews')->insert($datas);
        } 
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('interviews');
    }
}

