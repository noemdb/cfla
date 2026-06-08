<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateIncidentDutiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('incident_duties', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->comment("Nombre");
            $table->text('description')->comment("Descripción");
            $table->boolean('status_active')->default(true);
            $table->timestamps();
        });

        if (!DB::table('incident_duties')->exists()) {
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
            DB::table('incident_duties')->truncate();
            $datas = [
                [ 'name' => "Cumplir con el uniforme establecido", 'description' => "Cumplir con el uniforme establecido"],
                [ 'name' => "Cumplimiento de la jornada pedagógica", 'description' => "Cumplimiento de la jornada pedagógica"],
                [ 'name' => "Comportamiento acorde al perfil estudiante Amigoniano", 'description' => "Comportamiento acorde al perfil estudiante Amigoniano"],
                [ 'name' => "Cuidar y mantener los bienes pertenecientes a la institución", 'description' => "Cuidar y mantener los bienes pertenecientes a la institución"],
                [ 'name' => "Uso de la tecnología", 'description' => "Uso de la tecnología"],
                [ 'name' => "Buen Trato", 'description' => "Buen Trato"],
                [ 'name' => "Ética y Valores", 'description' => "Ética y Valores"],
                [ 'name' => "Salud Escolar", 'description' => "Salud Escolar"],
                [ 'name' => "Respeto del área de recreación", 'description' => "Respeto del área de recreación"],
            ] ;
            DB::table('incident_duties')->insert($datas);
        }        
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('incident_duties');
    }
}

/*
Cumplir con el uniforme establecido
Cumplimiento de la jornada pedagógica
Comportamiento acorde al perfil estudiante Amigoniano
Cuidar y mantener los bienes pertenecientes a la institución
Uso de la tecnología
Buen Trato
Ética y Valores
Salud Escolar
Respeto del área de recreación
*/