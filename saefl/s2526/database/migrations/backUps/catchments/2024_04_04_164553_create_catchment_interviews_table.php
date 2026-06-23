<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCatchmentInterviewsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('catchment_interviews', function (Blueprint $table) {
            $table->id();
            $table->integer('catchment_id')->nullable()->unsigned()->comment('Censo');

            //I.- Datos del Representante:
            $table->string('full_name')->comment('Nombres y Apellidos');
            $table->string('identification_number')->comment('Cédula de Identidad');
            $table->integer('age')->comment('Edad');
            $table->string('relationship')->comment('Parentesco');
            $table->string('phone_numbers')->comment('Teléfonos de contacto');
            $table->string('email')->comment('Correo electrónico');
            $table->string('profession_occupation')->comment('Profesión/Oficio');
            
            //II.- Datos del Estudiante Aspirante a Cupo:
            $table->string('student_full_name')->comment('Nombres y Apellidos del Estudiante');
            $table->date('date_of_birth')->comment('Fecha de Nacimiento');
            $table->integer('student_age')->comment('Edad del Estudiante');
            $table->integer('grade_year_aspiring')->comment('Grado/Año al que aspira ingresar');
            $table->boolean('has_siblings')->nullable()->default(false)->comment('¿Tiene hermanos estudiando en el Colegio?');
            $table->string('sibling_name')->nullable()->comment('Nombre del hermano/a');
            $table->integer('sibling_grade_section')->nullable()->comment('Grado/Año del hermano/a');
            $table->string('tutor_teacher_name')->nullable()->comment('Nombre del maestro/tutor');
            $table->string('tutor_teacher_phone')->nullable()->comment('Teléfono del maestro/tutor');

            //III.- Información sobre quién vive con el representado:         
            $table->enum('living_with', ['Madre', 'Padre', 'Ambos', 'Hermano(a)', 'Otro'])->comment('¿Con quién vive su representado?');
            $table->string('other_person_origin')->nullable()->comment('Procedencia si vive con otra persona');
            $table->string('reason_for_living_with_other')->nullable()->comment('Motivo por el cual vive con otra persona');            
            $table->integer('num_family_group_members')->comment('Número de integrantes del grupo familiar');
            $table->integer('num_people_financially_dependent')->comment('Número de personas que dependen económicamente');            
            $table->string('person_responsible_attending')->comment('Quién atiende, colabora o monitorea al representado');
            $table->string('place_person_responsible_attending')->comment('Lugar donde se atiende, colabora o monitorea al representado');
            $table->string('position_person_responsible_attending')->nullable()->comment('Cargo de la persona que atiende, colabora o monitorea');
            $table->string('work_person_responsible_attending')->nullable()->comment('Trabajo desempeñado por la persona que atiende, colabora o monitorea');
            
            //IV.- Aspectos Socio-Económicos:
            $table->string('monthly_income')->comment('Ingreso Mensual');
            $table->integer('num_people_contributing')->comment('Número de personas que aportan económicamente');
            $table->string('income_source')->comment('Fuente de los ingresos');
            $table->boolean('able_to_pay_dollars')->nullable()->default(false)->comment('¿Estaría en condiciones de cancelar una mensualidad en dólares?');
            $table->boolean('able_to_pay_bolivars')->nullable()->default(false)->comment('¿Estaría en condiciones de cancelar una mensualidad en bolívares al cambio del día?');
            $table->boolean('has_payment_responsible')->nullable()->default(false)->comment('Tiene usted una garante de respaldo, en caso de necesitarlo?');
            $table->string('person_guarantor_name_phone')->nullable()->comment('Nombre y teléfono del garante responsable de cancelar la deuda');

            //V.- Aspectos Institucionales:            
            $table->string('knowledge_of_school')->nullable()->comment('Desde cuándo conoce o ha escuchado hablar del colegio');
            $table->boolean('studied_at_school')->nullable()->default(false)->comment('¿Estudió usted en esta institución?');
            $table->integer('year_of_graduation')->nullable()->comment('Año de egreso del colegio');
            $table->string('academic_director')->nullable()->comment('Nombre del director académico en ese momento');
            $table->string('school_director')->nullable()->comment('Nombre del director del colegio en ese momento');
            $table->string('teachers_worked_at_school')->nullable()->comment('Nombre de al menos dos docentes que trabajen o hayan trabajado en el colegio');
            
            $table->text('reason_for_choosing_institution')->comment('Razón por la cual se escoge esta institución');
            $table->boolean('recommendation_from_school')->nullable()->default(false)->comment('¿Recibió alguna recomendación de parte de la Casa de Estudios?');
            $table->string('recommender_name')->nullable()->comment('Nombre y apellido de quien recomienda');
            $table->string('recommender_affinity')->nullable()->comment('Parentesco o afinidad con el colegio');
            $table->string('recommender_phone')->nullable()->comment('Teléfono de contacto del recomendante');
            
            //agreement
            $table->boolean('agreement_to_code_of_conduct')->default(false)->nullable()->comment('¿Acepta cumplir con los acuerdos de convivencia?');
            $table->boolean('respect_communication_channels')->default(false)->nullable()->comment('¿Respetará los canales de comunicación establecidos por el Colegio?');
            $table->boolean('ensure_compliance_with_school_activities')->default(false)->nullable()->comment('¿Velará por el cumplimiento de las actividades escolares?');
            $table->boolean('comply_with_school_uniform')->default(false)->nullable()->comment('¿Cumplirá con el uniforme escolar?');
            $table->boolean('respect_authorities_directives')->default(false)->nullable()->comment('¿Respetará las disposiciones de las autoridades del Colegio?');
            $table->boolean('pay_installments_on_time')->default(false)->nullable()->comment('¿Cancelará las cuotas o mensualidades al día?');
            $table->boolean('respect_parent_assembly_agreements')->default(false)->nullable()->comment('¿Respetará los acuerdos tomados en las asambleas de padres y representantes?');
            $table->boolean('pay_overdue_installments')->default(false)->nullable()->comment('¿Cancelará las mensualidades en mora al valor actual?');            
            $table->string('family_member_studied_worked_at_school')->nullable()->comment('Familiar que estudie o trabaje en el colegio');
            
            //VI.- Aspectos Religiosos:
            $table->string('religion')->nullable()->comment('¿Cuál es su religión?');
            $table->boolean('awareness_of_catholic_school_affiliation')->default(false)->nullable()->comment('¿Está consciente de que nuestro colegio se rige bajo preceptos de la Iglesia Católica? De ser negativa su respuesta, indique qué religión profesa.');
            $table->boolean('agreement_to_catholic_formation')->default(false)->nullable()->comment('¿Está de acuerdo con la formación católica que se imparte en el colegio?');
            $table->boolean('agreement_to_participate_in_catholic_activities')->default(false)->nullable()->comment('¿Está de acuerdo en que participe en todas las actividades que indique la iglesia católica y nuestra institución donde se exalte nuestro carisma y religiosidad?');
            $table->string('justification_for_not_participating_in_catholic_activities')->nullable()->comment('De ser negativa su respuesta justifique su respuesta');
            
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
        Schema::dropIfExists('catchment_interviews');
    }
}
