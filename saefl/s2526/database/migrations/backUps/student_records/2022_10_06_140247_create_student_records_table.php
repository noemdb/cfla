<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStudentRecordsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('student_records', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('estudiant_id')->unique()->unsigned()->comment('Estudiante');
            $table->string('coexistence')->nullable()->comment('Con quién vive?');
            $table->boolean('status_transport_private_vehicle')->nullable()->comment('Vehículo particular');
            $table->boolean('status_transport_public_vehicle')->nullable()->comment('Trasnporte público');
            $table->boolean('status_transport_walking')->nullable()->comment('Caminando');
            $table->boolean('status_transport_other')->nullable()->comment('Otro');
            $table->string('transport_other')->nullable()->comment('Otro medio de transporte');
            $table->boolean('status_vaccination_schedule')->nullable()->comment('Esquema de vacunación');
            $table->boolean('status_sports_potential')->nullable()->comment('Potencial deportivo');
            $table->string('sports_potential')->nullable()->comment('Especifique actividad deportiva/cultural u otro');
            $table->string('place_where_he_practices')->nullable()->comment('Lugar dónde practica');
            $table->boolean('status_illness_cardiovascular')->nullable()->comment('Cardiovascular');
            $table->boolean('status_illness_cancer')->nullable()->comment('Cáncer');
            $table->boolean('status_illness_lupus')->nullable()->comment('Lupus');
            $table->boolean('status_illness_diabetes')->nullable()->comment('Diabetes');
            $table->boolean('status_illness_renal_problems')->nullable()->comment('Problemas Renales');
            $table->boolean('status_illness_overweight')->nullable()->comment('Sobrepeso');
            $table->boolean('status_illness_other')->nullable()->comment('Otra (Especifique)');
            $table->string('illness_other')->nullable()->comment('Otra enfermedad de gravedad');
            $table->boolean('status_conditions_intellectual_disability')->nullable()->comment('Discapacidad intelectual');
            $table->boolean('status_conditions_motor_disability')->nullable()->comment('Discapacidad motriz');
            $table->boolean('status_conditions_visual_disability')->nullable()->comment('Discapacidad visual');
            $table->boolean('status_conditions_hearing_impairment')->nullable()->comment('Discapacidad auditiva');
            $table->boolean('status_conditions_outstanding_attitudes')->nullable()->comment('Actitudes sobresalientes');
            $table->boolean('status_conditions_autism')->nullable()->comment('Autismo');
            $table->boolean('status_conditions_other')->nullable()->comment('Otra (especifique)');
            $table->string('conditions_other')->nullable()->comment('Otra condición de gravedad');
            $table->boolean('status_treated_by_specialist')->nullable()->comment('Tratado por algún especialista');
            $table->string('specialist')->nullable()->comment('Especialista');
            $table->boolean('status_take_medication')->nullable()->comment('Toma algún medicamento');
            $table->string('medication')->nullable()->comment('Medicamento');
            $table->string('mother_name')->comment('Nombres de La madre')->nullable();
            $table->string('mother_lastname')->comment('Apellidos de la madre')->nullable();
            $table->string('mother_ci')->comment('Cédula de la madre')->nullable();
            $table->string('mother_profession')->comment('Profesión de la madre')->nullable();
            $table->string('mother_phones')->comment('teléfonos de la madre')->nullable();
            $table->string('mother_address')->comment('Dirección y contacto del trabajo de la madre')->nullable();
            $table->string('father_name')->comment('Nombres del padre')->nullable();
            $table->string('father_lastname')->comment('Apellidos del padre')->nullable();
            $table->string('father_ci')->comment('Cédula del padre')->nullable();
            $table->string('father_profession')->comment('Profesión de la padre')->nullable();
            $table->string('father_phones')->comment('teléfonos del padre')->nullable();
            $table->string('father_address')->comment('Dirección y contacto del trabajo de la padre')->nullable();

            $table->timestamps();
            $table->foreign('estudiant_id')->references('id')->on('estudiants')->onDelete('cascade')->onUpdate('cascade');
        });

        /*

intellectual_disability ( )
motor_disability ( )
visual_disability (  )
hearing_impairment ( )
outstanding_attitudes ( )
autism ( )
other (specify)

Discapacidad intelectual (  )
Discapacidad motriz (  )
Discapacidad visual (  )
Discapacidad auditiva (  )
Actitudes sobresalientes (  )
Autismo (  ) 
Otra (especifique)

        
        */
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('student_records');
    }
}
