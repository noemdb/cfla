<?php

namespace App\Models\app\Enrollment;

use App\Models\app\Pescolar\Grado;
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
        'email_alternate',
    ];

protected $casts = [
        'has_siblings' => 'boolean',
        'able_to_pay_dollars' => 'boolean',
        'able_to_pay_bolivars' => 'boolean',
        'has_payment_responsible' => 'boolean',
        'studied_at_school' => 'boolean',
        'recommendation_from_school' => 'boolean',
        'agreement_to_code_of_conduct' => 'boolean',
        'respect_communication_channels' => 'boolean',
        'ensure_compliance_with_school_activities' => 'boolean',
        'comply_with_school_uniform' => 'boolean',
        'respect_authorities_directives' => 'boolean',
        'pay_installments_on_time' => 'boolean',
        'respect_parent_assembly_agreements' => 'boolean',
        'pay_overdue_installments' => 'boolean',
        'family_member_studied_worked_at_school' => 'boolean',
        'awareness_of_catholic_school_affiliation' => 'boolean',
        'agreement_to_catholic_formation' => 'boolean',
        'agreement_to_participate_in_catholic_activities' => 'boolean',
    ];

    // 🔹 Mutators para garantizar que NULL → false
    public function setHasSiblingsAttribute($value)
    {
        $this->attributes['has_siblings'] = $value ?? false;
    }

    public function setAbleToPayDollarsAttribute($value)
    {
        $this->attributes['able_to_pay_dollars'] = $value ?? false;
    }

    public function setAbleToPayBolivarsAttribute($value)
    {
        $this->attributes['able_to_pay_bolivars'] = $value ?? false;
    }

    public function setHasPaymentResponsibleAttribute($value)
    {
        $this->attributes['has_payment_responsible'] = $value ?? false;
    }

    public function setStudiedAtSchoolAttribute($value)
    {
        $this->attributes['studied_at_school'] = $value ?? false;
    }

    public function setRecommendationFromSchoolAttribute($value)
    {
        $this->attributes['recommendation_from_school'] = $value ?? false;
    }

    public function setAgreementToCodeOfConductAttribute($value)
    {
        $this->attributes['agreement_to_code_of_conduct'] = $value ?? false;
    }

    public function setRespectCommunicationChannelsAttribute($value)
    {
        $this->attributes['respect_communication_channels'] = $value ?? false;
    }

    public function setEnsureComplianceWithSchoolActivitiesAttribute($value)
    {
        $this->attributes['ensure_compliance_with_school_activities'] = $value ?? false;
    }

    public function setComplyWithSchoolUniformAttribute($value)
    {
        $this->attributes['comply_with_school_uniform'] = $value ?? false;
    }

    public function setRespectAuthoritiesDirectivesAttribute($value)
    {
        $this->attributes['respect_authorities_directives'] = $value ?? false;
    }

    public function setPayInstallmentsOnTimeAttribute($value)
    {
        $this->attributes['pay_installments_on_time'] = $value ?? false;
    }

    public function setRespectParentAssemblyAgreementsAttribute($value)
    {
        $this->attributes['respect_parent_assembly_agreements'] = $value ?? false;
    }

    public function setPayOverdueInstallmentsAttribute($value)
    {
        $this->attributes['pay_overdue_installments'] = $value ?? false;
    }

    public function setFamilyMemberStudiedWorkedAtSchoolAttribute($value)
    {
        $this->attributes['family_member_studied_worked_at_school'] = $value ?? false;
    }

    public function setAwarenessOfCatholicSchoolAffiliationAttribute($value)
    {
        $this->attributes['awareness_of_catholic_school_affiliation'] = $value ?? false;
    }

    public function setAgreementToCatholicFormationAttribute($value)
    {
        $this->attributes['agreement_to_catholic_formation'] = $value ?? false;
    }

    public function setAgreementToParticipateInCatholicActivitiesAttribute($value)
    {
        $this->attributes['agreement_to_participate_in_catholic_activities'] = $value ?? false;
    }

    const COLUMN_COMMENTS = [
        'catchment_id' => 'Registro del Censo',
        'token' => 'Token',
        'full_name' => 'Nombres y Apellidos',
        'identification_number' => 'Número de Cédula',
        'age' => 'Edad',
        'relationship' => 'Parentesco',
        'phone_numbers' => 'Números de teléfono',
        'email' => 'Correo electrónico',
        'email_alternate' => 'Correo electrónico alterno',
        'profession_occupation' => 'Profesión/Ocupación',
        'student_full_name' => 'Nombres y Apellidos del Estudiante',
        'date_of_birth' => 'Fecha de Nacimiento',
        'student_age' => 'Edad del Estudiante',
        'grade_year_aspiring' => 'Grado/Año al que aspira',
        'has_siblings' => '¿Tiene hermanos estudiando en el Colegio?',
        'sibling_name' => 'Nombre del Hermano/a',
        'sibling_grade_section' => 'Grado/Sección del Hermano/a',
        'tutor_teacher_name' => 'Nombres del Tutor/Docente',
        'tutor_teacher_phone' => 'Teléfono del Tutor/Docente',
        'living_with' => '¿Con quién vive?',
        'other_person_origin' => 'Otra procedencia de convivencia',
        'reason_for_living_with_other' => 'Motivo de convivencia con otra persona',
        'num_family_group_members' => 'Número de miembros del grupo familiar',
        'num_people_financially_dependent' => 'Número de personas dependientes económicamente',
        'person_responsible_attending' => 'Persona responsable de asistir al Colegio',
        'place_person_responsible_attending' => 'Lugar de trabajo de la persona responsable de asistir al Colegio',
        'position_person_responsible_attending' => 'Cargo de la persona responsable de asistir al Colegio',
        'work_person_responsible_attending' => 'Trabajo de la persona responsable de asistir al Colegio',
        'monthly_income' => 'Ingreso mensual',
        'num_people_contributing' => 'Número de personas que contribuyen',
        'income_source' => 'Fuente de ingresos',
        'able_to_pay_dollars' => '¿Estaría usted en condiciones de asumir la mensualidad escolar vigente (USD 120), considerando que para el próximo período pudiera aplicarse un ajuste estimado entre un 10% y un 15%?',
        'able_to_pay_bolivars' => '¿O en su defecto cancelar en su equivalente en Bolívares al cambio del día del BCV?',
        'has_payment_responsible' => 'En caso de que por cualquier razón usted se retrase en el pago los primeros cinco (05) días de cada mes, ¿tendría usted alguna persona que se pudiera hacer responsable de cancelar esa deuda?',
        'person_guarantor_name_phone' => 'Nombre y teléfono de la persona:',
        'knowledge_of_school' => '¿Desde cuándo conoce o ha escuchado hablar del Colegio Fray Luis Amigó?',
        'studied_at_school' => '¿Estudió usted en esta institución?',
        'year_of_graduation' => 'Año de graduación',
        'academic_director' => 'Director(a) Académico(a) para ese entonces',
        'school_director' => 'Director(a) del Colegio para ese entonces',
        'teachers_worked_at_school' => 'Nombre al menos 2 docentes que trabajen o hayan trabajado en esta institución',
        'reason_for_choosing_institution' => '¿Por qué razón escoge nuestra institución para la prosecución de estudios de su representado?',
        'recommendation_from_school' => '¿En algún momento, alguien le recomendó nuestra casa de estudios?',
        'recommender_name' => 'Nombre de la persona que recomienda',
        'recommender_affinity' => 'Parentesco o afinidad con la persona que recomienda',
        'recommender_phone' => 'Teléfono de la persona que recomienda',
        'agreement_to_code_of_conduct' => 'Cumplir con los Acuerdos de Convivencia de la Institución',
        'respect_communication_channels' => 'Respetar los canales de comunicación',
        'ensure_compliance_with_school_activities' => 'Garantizar el cumplimiento de las actividades del Colegio',
        'comply_with_school_uniform' => 'Cumplir con el uniforme escolar',
        'respect_authorities_directives' => 'Respetar las directrices de las autoridades',
        'pay_installments_on_time' => 'Pagar las cuotas dentro de los primeros cinco (05) días hábiles de cada mes',
        'respect_parent_assembly_agreements' => 'Respetar los acuerdos de la Asamblea de Padres',
        'pay_overdue_installments' => 'Intereses de Mora: El representante acepta la responsabilidad de cancelar un recargo equivalente al 1% mensual por retraso en el pago de cualquier cuota por escolaridad',
        'family_member_studied_worked_at_school' => 'Tiene un miembro de la familia que ha estudiado o trabajado en el Colegio',
        'religion' => '¿Cuál es su religión?',
        'awareness_of_catholic_school_affiliation' => '¿Está consciente de que nuestro colegio se rige bajo preceptos de la Iglesia Católica?',
        'agreement_to_catholic_formation' => '¿Está de acuerdo con la formación católica y con que a su representado reciba, como parte del programa educativo , la asignatura de Formación Humana Cristiana?',
        'agreement_to_participate_in_catholic_activities' => '¿Está usted de acuerdo en que su hijo(a) participe activamente en todas las actividades previstas en el calendario religioso de la institución?',
        'justification_for_not_participating_in_catholic_activities' => 'Justifique su respuesta',
        'accepted' => '¿Aceptado?',
        'rating' => 'Calificación otorgada a la entrevista [1-5]',
        'observations' => 'Observaciones',
        'status_notified' => 'Notificado',
        'status_standby' => 'Estado en espera?',
        'sibling_name_2' => 'Nombre del segundo hermano/a',
        'sibling_name_3' => 'Nombre del tercer hermano/a',
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

    public function formatBoolean($value)
    {
        if (is_null($value) || $value === '') {
            return '';
        }
        return $value == true ? 'Sí' : 'No';
    }

    public function generateToken()
    {
        if (empty($this->token)) {
            $randomBytes = bin2hex(random_bytes(45));
            $hashed = password_hash($randomBytes, PASSWORD_BCRYPT);
            $this->token = substr(str_replace(['+', '/', '=', '&'], '', $hashed), 0, 32);
        }
        return $this->token;
    }

    public static function list_monthly_income()
    {
        return [
            'Salario minimo' => 'Salario minimo',
            'Entre 10 y 70$' => 'Entre 10 y 70$',
            'Entre  70 y 150$' => 'Entre  70 y 150$',
            '150$ o más' => '150$ o más',
        ];
    }

    public static function list_religions()
    {
        return [
            "Católica" => "Católica",
            "Protestante" => "Protestante",
            "Judía" => "Judía",
            "Musulmana" => "Musulmana",
            "Evangélica" => "Evangélica",
            "Ortodoxa" => "Ortodoxa",
            "Budista" => "Budista",
            "Hinduista" => "Hinduista",
            "Taoísta" => "Taoísta",
            "Atea" => "Atea",
            "Agnóstica" => "Agnóstica",
            "Otra" => "Otra"
        ];
    }

    public static function list_grade()
    {
        return [
            '20000-EDUCACION INICIAL' => [
                '22' => '1ER GRUPO',
                '23' => '2DO GRUPO',
                '24' => '3ER GRUPO',
            ],
            '21000-EDUCACION PRIMARIA' => [
                '1' => '1ER GRADO',
                '2' => '2DO GRADO',
                '3' => '3ER GRADO',
                '4' => '4TO GRADO',
                '5' => '5TO GRADO',
                '6' => '6TO GRADO',
            ],
            '31059-EDUCACION MEDIA GENERAL' => [
                '10' => 'CUARTO AÑO',
                // '11' => 'QUINTO AÑO',
            ],
            '31059B-EDUCACION MEDIA GENERAL CIENCIA Y TECNOLOGIA' => [
                '12' => 'PRIMER AÑO',
                '13' => 'SEGUNDO AÑO',
                '14' => 'TERCER AÑO',
            ],
        ];
    }

    public static function getAccepteds()
    {
        return CatchmentInterview::query()
            ->select('catchment_interviews.*')
            ->where('catchment_interviews.accepted', true)
            ->get()
            // ->take(1)
        ;
    }
}
