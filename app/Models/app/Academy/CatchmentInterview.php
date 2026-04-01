<?php
namespace App\Models\app\Academy;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CatchmentInterview extends Model
{
    use HasFactory;

    protected $fillable = [
        'catchment_id',
        'token',
        'full_name',
        'identification_number',
        'age',
        'relationship',
        'phone_numbers',
        'email',
        'profession_occupation',
        'student_full_name',
        'date_of_birth',
        'student_age',
        'grade_year_aspiring',
        'has_siblings',
        'sibling_name',
        'sibling_grade_section',
        'tutor_teacher_name',
        'tutor_teacher_phone',
        'living_with',
        'other_person_origin',
        'reason_for_living_with_other',
        'num_family_group_members',
        'num_people_financially_dependent',
        'person_responsible_attending',
        'place_person_responsible_attending',
        'position_person_responsible_attending',
        'work_person_responsible_attending',
        'monthly_income',
        'num_people_contributing',
        'income_source',
        'able_to_pay_dollars',
        'able_to_pay_bolivars',
        'has_payment_responsible',
        'person_guarantor_name_phone',
        'knowledge_of_school',
        'studied_at_school',
        'year_of_graduation',
        'academic_director',
        'school_director',
        'teachers_worked_at_school',
        'reason_for_choosing_institution',
        'recommendation_from_school',
        'recommender_name',
        'recommender_affinity',
        'recommender_phone',
        'agreement_to_code_of_conduct',
        'respect_communication_channels',
        'ensure_compliance_with_school_activities',
        'comply_with_school_uniform',
        'respect_authorities_directives',
        'pay_installments_on_time',
        'respect_parent_assembly_agreements',
        'pay_overdue_installments',
        'family_member_studied_worked_at_school',
        'agreement_to_catholic_formation',
        'awareness_of_catholic_school_affiliation',
        'religion',
        'agreement_to_participate_in_catholic_activities',
        'justification_for_not_participating_in_catholic_activities',
        'accepted',
        'rating',
        'observations',
        'status_notified',
        'status_standby',
        'sibling_name_2',
        'sibling_name_3',
    ];

    const COLUMN_COMMENTS = [
        'catchment_id'                                               => 'Registro del Censo',
        'token'                                                      => 'Token',
        'full_name'                                                  => 'Nombres y Apellidos',
        'identification_number'                                      => 'Número de Cédula',
        'age'                                                        => 'Edad',
        'relationship'                                               => 'Parentesco',
        'phone_numbers'                                              => 'Números de teléfono',
        'email'                                                      => 'Correo electrónico',
        'profession_occupation'                                      => 'Profesión/Ocupación',
        'student_full_name'                                          => 'Nombres y Apellidos del Estudiante',
        'date_of_birth'                                              => 'Fecha de Nacimiento',
        'student_age'                                                => 'Edad del Estudiante',
        'grade_year_aspiring'                                        => 'Grado/Año al que aspira',
        'has_siblings'                                               => '¿Tiene hermanos estudiando en el Colegio?',
        'sibling_name'                                               => 'Nombre del Hermano/a',
        'sibling_grade_section'                                      => 'Grado/Sección del Hermano/a',
        'tutor_teacher_name'                                         => 'Nombres del Tutor/Docente',
        'tutor_teacher_phone'                                        => 'Teléfono del Tutor/Docente',
        'living_with'                                                => '¿Con quién vive?',
        'other_person_origin'                                        => 'Otra procedencia de convivencia',
        'reason_for_living_with_other'                               => 'Motivo de convivencia con otra persona',
        'num_family_group_members'                                   => 'Número de miembros del grupo familiar',
        'num_people_financially_dependent'                           => 'Número de personas dependientes económicamente',
        'person_responsible_attending'                               => 'Persona responsable de asistir al Colegio',
        'place_person_responsible_attending'                         => 'Lugar de trabajo de la persona responsable de asistir al Colegio',
        'position_person_responsible_attending'                      => 'Cargo de la persona responsable de asistir al Colegio',
        'work_person_responsible_attending'                          => 'Trabajo de la persona responsable de asistir al Colegio',
        'monthly_income'                                             => 'Ingreso mensual',
        'num_people_contributing'                                    => 'Número de personas que contribuyen',
        'income_source'                                              => 'Fuente de ingresos',
        'able_to_pay_dollars'                                        => '¿Estaría usted en condiciones de cancelar una mensualidad entre 90 y 120 USD?',
        'able_to_pay_bolivars'                                       => '¿O en su defecto cancelar en su equivalente en Bolívares al cambio del día del BCV?',
        'has_payment_responsible'                                    => 'En caso de que por cualquier razón usted se retrase en el pago los primeros cinco (05) días de cada mes, ¿tendría usted alguna persona que se pudiera hacer responsable de cancelar esa deuda?',
        'person_guarantor_name_phone'                                => 'Nombre y teléfono de la persona:',
        'knowledge_of_school'                                        => '¿Desde cuándo conoce o ha escuchado hablar del Colegio Fray Luis Amigó?',
        'studied_at_school'                                          => '¿Estudió usted en esta institución?',
        'year_of_graduation'                                         => 'Año de graduación',
        'academic_director'                                          => 'Director(a) Académico(a) para ese entonces',
        'school_director'                                            => 'Director(a) del Colegio para ese entonces',
        'teachers_worked_at_school'                                  => 'Nombre al menos 2 docentes que trabajen o hayan trabajado en esta institución',
        'reason_for_choosing_institution'                            => '¿Por qué razón escoge nuestra institución para la prosecución de estudios de su representado?',
        'recommendation_from_school'                                 => '¿En algún momento, alguien le recomendó nuestra casa de estudios?',
        'recommender_name'                                           => 'Nombre de la persona que recomienda',
        'recommender_affinity'                                       => 'Parentesco o afinidad con la persona que recomienda',
        'recommender_phone'                                          => 'Teléfono de la persona que recomienda',
        'agreement_to_code_of_conduct'                               => 'Cumplir con los Acuerdos de Convivencia de la Institución',
        'respect_communication_channels'                             => 'Respetar los canales de comunicación',
        'ensure_compliance_with_school_activities'                   => 'Garantizar el cumplimiento de las actividades del Colegio',
        'comply_with_school_uniform'                                 => 'Cumplir con el uniforme escolar',
        'respect_authorities_directives'                             => 'Respetar las directrices de las autoridades',
        'pay_installments_on_time'                                   => 'Pagar las cuotas a tiempo',
        'respect_parent_assembly_agreements'                         => 'Respetar los acuerdos de la Asamblea de Padres',
        'pay_overdue_installments'                                   => 'Pagar cuotas vencidas',
        'family_member_studied_worked_at_school'                     => 'Miembro de la familia que ha estudiado o trabajado en el Colegio',
        'religion'                                                   => '¿Cuál es su religión?',
        'awareness_of_catholic_school_affiliation'                   => '¿Está consciente de que nuestro colegio se rige bajo preceptos de la Iglesia Católica?',
        'agreement_to_catholic_formation'                            => '¿Está de acuerdo con la formación católica?',
        'agreement_to_participate_in_catholic_activities'            => '¿Está de acuerdo en que su representado participe en todas las actividades que indique la iglesia católica y nuestra institución donde se exalte nuestro carisma y religiosidad?',
        'justification_for_not_participating_in_catholic_activities' => 'Justifique su respuesta',
        'accepted'                                                   => '¿Aceptado?',
        'rating'                                                     => 'Calificación otorgada a la entrevista [1-5]',
        'observations'                                               => 'Observaciones',
        'status_notified'                                            => 'Notificado',
        'status_standby'                                             => 'Estado en espera?',
        'sibling_name_2'                                             => 'Nombre del segundo hermano/a',
        'sibling_name_3'                                             => 'Nombre del tercer hermano/a',
    ];

    public function catchment()
    {
        return $this->belongsTo(Catchment::class, 'catchment_id');
    }
    public function grado()
    {
        return $this->belongsTo(Grado::class, 'grade_year_aspiring');
    }
    public function grado_sibling()
    {
        return $this->belongsTo(Grado::class, 'sibling_grade_section');
    }

    public static function testimonials($limit = 3)
    {
        return CatchmentInterview::query()
            ->whereRaw('LENGTH(reason_for_choosing_institution) > 100')
            ->orderByRaw('RAND()')
            ->limit($limit)
            ->get();
    }
}
