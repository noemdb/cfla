<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddStudentRecordsToEnrollmensTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('enrollments', function (Blueprint $table) {
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
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('enrollments', function (Blueprint $table) {
            $table->dropColumn('coexistence');
            $table->dropColumn('status_transport_private_vehicle');
            $table->dropColumn('status_transport_public_vehicle');
            $table->dropColumn('status_transport_walking');
            $table->dropColumn('status_transport_other');
            $table->dropColumn('transport_other');
            $table->dropColumn('status_vaccination_schedule');
            $table->dropColumn('status_sports_potential');
            $table->dropColumn('sports_potential');
            $table->dropColumn('place_where_he_practices');
            $table->dropColumn('status_illness_cardiovascular');
            $table->dropColumn('status_illness_cancer');
            $table->dropColumn('status_illness_lupus');
            $table->dropColumn('status_illness_diabetes');
            $table->dropColumn('status_illness_renal_problems');
            $table->dropColumn('status_illness_overweight');
            $table->dropColumn('status_illness_other');
            $table->dropColumn('illness_other');
            $table->dropColumn('status_conditions_intellectual_disability');
            $table->dropColumn('status_conditions_motor_disability');
            $table->dropColumn('status_conditions_visual_disability');
            $table->dropColumn('status_conditions_hearing_impairment');
            $table->dropColumn('status_conditions_outstanding_attitudes');
            $table->dropColumn('status_conditions_autism');
            $table->dropColumn('status_conditions_other');
            $table->dropColumn('conditions_other');
            $table->dropColumn('status_treated_by_specialist');
            $table->dropColumn('specialist');
            $table->dropColumn('status_take_medication');
            $table->dropColumn('medication');
            $table->dropColumn('mother_name');
            $table->dropColumn('mother_lastname');
            $table->dropColumn('mother_ci');
            $table->dropColumn('mother_profession');
            $table->dropColumn('mother_phones');
            $table->dropColumn('mother_address');
            $table->dropColumn('father_name');
            $table->dropColumn('father_lastname');
            $table->dropColumn('father_ci');
            $table->dropColumn('father_profession');
            $table->dropColumn('father_phones');
            $table->dropColumn('father_address');
        });
    }
}
