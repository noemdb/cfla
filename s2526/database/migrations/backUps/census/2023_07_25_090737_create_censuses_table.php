<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCensusesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('censuses', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('user_id')->nullable()->comment('Usuario');
            $table->string('ci_estudiant')->comment('Cédula de identidad');
            $table->string('lastname')->nullable()->comment('Apellidos');
            $table->string('name')->nullable()->comment('Nombres');

            $table->string('cellphone')->nullable()->comment('Número de teléfono celular');

            $table->enum('gender',['Masculino', 'Femenino'])->comment('Género');//Másculino,Femenino
            $table->date('date_birth')->comment('Fecha de nacimiento');
            $table->smallInteger('age')->unsigned()->comment('Edad');
            $table->string('town_hall_birth')->comment('Municipio de nacimiento');
            $table->string('state_birth')->comment('Estado de nacimiento');
            $table->string('country_birth')->comment('País de nacimiento');
            $table->string('dir_address')->comment('Dirección de residencia');

            $table->enum('pestudio_id',['1', '2'])->comment('Programa de Estudio');//Másculino,Femenino
            $table->enum('grado_id',['1','2','3','4','5','6','7','8','9','10','11'])->comment('Curso');//Másculino,Femenino
            $table->smallInteger('grupo_estable_id')->unsigned()->comment('Grupo Estable');
            $table->string('pending_matter')->nullable()->comment('Materia Pendiente separadas por comas');

            $table->enum('blood_type',['A','B','O'])->comment('Programa de Estudio');//Másculino,Femenino
            $table->smallInteger('weight')->unsigned()->comment('Peso');
            $table->smallInteger('height')->unsigned()->comment('Estatura');
            $table->enum('laterality',['IZQUIERDA', 'DERECHA'])->comment('Lateralidad');
            $table->string('institution')->nullable()->comment('Institución Procedencia');
            $table->enum('order_born',['1','2','3','4','5','6','7','8','9','10'])->comment('Orden de Nacimiento');
            $table->smallInteger('group_family')->unsigned()->comment('Grupo Familiar');

            $table->enum('status_brother',['true','false'])->default('true')->comment('Hermanos en el colegio');
            $table->enum('literal',['A','B','O'])->comment('Literal de promosión, (s{olo primaria)');//Másculino,Femenino

            $table->string('ci_representant')->comment('Cédula de identidad Representante');
            $table->string('name_representant')->comment('Nombres de Representante');
            $table->enum('relationship',['Madre', 'Padre','Hermano(a)','Tío(a)','Abuelo(a)','Otro'])->comment('Lateralidad');
            $table->string('profession_representant')->comment('Profesión');
            $table->string('phone_representant')->comment('Número de teléfono fijo');
            $table->string('cellphone_representant')->comment('Número de teléfono celular, [WhatsApp, Telegram]');
            $table->string('email_representant')->comment('Correo electrónico');
            $table->string('twitter')->nullable()->comment('Usuario Twitter');
            $table->string('instagram')->nullable()->comment('Usuario Instagram');
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
        Schema::dropIfExists('censuses');
    }
}
