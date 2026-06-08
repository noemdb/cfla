<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCatchmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('catchments', function (Blueprint $table) {
            $table->id();
            $table->integer('group_id')->nullable()->unsigned()->comment('Grupo');
            $table->string('token')->comment('Token');
            $table->string('firstname')->nullable()->comment('Nombre del estudiante');
            $table->string('lastname')->nullable()->comment('Apellido del estudiante');
            $table->string('grade')->nullable()->comment('Grado escolar');
            $table->date('date_birth')->nullable()->comment('Fecha de nacimiento');
            $table->string('gender')->nullable()->comment('Sexo');
            $table->string('origin')->nullable()->comment('Institución de procedencia');
            $table->string('representant_name')->comment('Nombre del representante');
            $table->string('representant_lastname')->comment('Apellido del representante');
            $table->string('representant_ci')->comment('Cédula de Identidad');
            $table->string('email')->comment('Correo electrónico');
            $table->string('relationship')->nullable()->comment('Parentesco con el estudiante');
            $table->string('occupation')->nullable()->comment('Ocupación');
            $table->string('educational_level')->nullable()->comment('Nivel educativo');
            $table->string('representant_phone')->nullable()->comment('Teléfono');
            $table->string('direction')->nullable()->comment('Dirección');            
            $table->text('reason_catholic')->nullable()->comment('¿Por qué está interesado en una institución escolar católica?');
            $table->text('reason_interest')->nullable()->comment('¿Por qué está interesado en nuestra institución escolar?');
            $table->text('aspects_valued')->nullable()->comment('¿Qué aspectos valora de nuestra institución escolar?');
            $table->text('expectations')->nullable()->comment('¿Qué espera de nuestra institución escolar?');
            $table->text('importance_education')->nullable()->comment('¿Qué importancia le da a la educación de su hijo/a?');
            $table->text('expectations_education')->nullable()->comment('¿Cuáles son sus expectations para la educación de su hijo/a?');
            $table->string('participation_activities')->nullable()->comment('¿Está dispuesto/a a participar en las actividades de la institución escolar?');
            $table->text('skills_talents')->nullable()->comment('¿Cuáles son las habilidades y talentos de su hijo/a?');
            $table->text('interests')->nullable()->comment('¿Cuáles son los intereses de su representado?');
            $table->text('challenges')->nullable()->comment('¿Cuáles son los desafíos que enfrenta su representado?');
            $table->boolean('status_active')->nullable()->default(true)->comment('Estado');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('catchments');
    }
}
