<?php

namespace App\Models\app\Bienestar;

use App\Models\app\Estudiant;
use App\Models\app\Pescolar\Profesor;
use App\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Jenssegers\Date\Date;

class IncidentOld extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'user_id','estudiant_id', 'profesor_id', 'reason_id', 'type', 'description','description_profesor', 'observations', 'taken_actions', 'status_aggression','status_reiterative', 'status_notify',
        'date_notify_email', 'date_notify_agreement_email', 'status_notify_agreement', 'status_announcement', 'status_active', 'hour_announcement',
        'date_announcement','status_close','status_pedagogical','close_observations','status_notify_close','date_close'
    ];

    protected $dates = ['created_at', 'updated_at', 'date_notify_email', 'date_notify_agreement_email','date_close'];

    protected $casts = [
        'date_announcement' => 'date:Y-m-d',
        // 'hour_announcement' => 'date:H:i:s'
    ];

    const COLUMN_COMMENTS = [
        'user_id'=>'Usuario',
        'estudiant_id' => 'Estudiante',
        'profesor_id' => 'Profesor',
        'reason_id' => 'Motivo',
        'type' => 'Tipo/Ámbito',
        'status_valoration' => 'Valoración (Positiva/Negativa)',
        'description' => 'Descripción [Tabulada]',
        'description_profesor' => 'Descripción en 1ra intancia',
        'observations' => 'Observaciones',
        'taken_actions' => 'Acciones tomadas',
        'status_aggression' => 'Presentó una conducta desafiante o disruptiva',
        'status_reiterative' => 'Reiterativa',
        'status_notify' => 'Notificada',
        'date_notify_email' => 'Fecha de la Notifición',
        'status_active' => 'Estado',
        'date_notify_agreement_email' => 'Fecha de notificación de los acuerdos',
        'status_notify_agreement' => 'Notificación de acuerdo',
        'status_announcement' => 'Se convoca al representante',
        'hour_announcement' => 'Hora Programada',
        'date_announcement' => 'Fecha Programada',
        'status_close' => 'Incidente cerrado',
        'status_close_filter' => 'Incidente cerrado',
        'status_pedagogical' => 'Correctivo pedagógico',
        'close_observations' => 'Observación de cierre',
        'status_notify_close' => 'Notificación de incidente cerrado',
        'finicial' => 'Fecha inicial',
        'ffinal' => 'Fecha final',
        'date_close' => 'Fecha de cierre',
        'help_description' => 'Descripción ...',
    ];

    public static function list_type()
    {
        return [
            'Académico' => 'Académico',
            'Disciplinario' => 'Disciplinario',
            // 'Académica y Disciplinaria' => 'Académica y Disciplinaria',
            'Social' => 'Social',
            'Emocional' => 'Emocional',
            'Familiar' => 'Familiar',
            'Personal' => 'Personal',
            'Otro' => 'Otro',
        ];
    }

    public static function list_valoration()
    {
        return ['1' => 'Positiva', '0' => 'Negativa'];
    }

    public static function list_status_close()
    {
        return [true => 'SI', false => 'NO'];
    }

    public static function list_description()
    {
        return [
            'Algunas veces el estudiante ha tenido dificultades para completar las actividades asignadas en clase dentro del plazo establecido.' => 'Algunas veces el estudiante ha tenido dificultades para completar las actividades asignadas en clase dentro del plazo establecido.',
            'A veces el estudiante puede distraerse durante las explicaciones del docente en clase.' => 'A veces el estudiante puede distraerse durante las explicaciones del docente en clase.',
            'Se ha observado que quizá el estudiante no atiende de manera efectiva al respeto y consideración hacia los demás.' => 'Se ha observado que quizá el estudiante no atiende de manera efectiva al respeto y consideración hacia los demás.',
            'Se ha observado que quizá el estudiante no ha comprendido el respeto y el cuidado de las instalaciones y materiales de la institución.' => 'Se ha observado que quizá el estudiante no ha comprendido el respeto y el cuidado de las instalaciones y materiales de la institución.',
            'A veces el estudiante puede olvidar lo importante de que todos los estudiantes tengan la oportunidad de participar en las clases y actividades escolares.' => 'A veces el estudiante puede olvidar lo importante de que todos los estudiantes tengan la oportunidad de participar en las clases y actividades escolares.',
            'Es importante cuidar nuestro lenguaje y ser respetuosos con los demás.' => 'Es importante cuidar nuestro lenguaje y ser respetuosos con los demás.',
            'A veces el estudiante puede olvidar lo importante de que todos los estudiantes se cuiden y protejan mutuamente.' => 'A veces el estudiante puede olvidar lo importante de que todos los estudiantes se cuiden y protejan mutuamente.',
            'A veces el estudiante puede olvidar lo importante de que todos los estudiantes se traten con respeto y consideración.' => 'A veces el estudiante puede olvidar lo importante de que todos los estudiantes se traten con respeto y consideración.',
        ];
    }    

    // public static function list_description_tab()
    // {
    //     $arr_negative = array(
    //         "Dificultades académicas y atención en clase" => array(
    //             "En ocasiones, el estudiante ha experimentado dificultades para completar las actividades asignadas en clase dentro del plazo establecido.",
    //             "En algunas situaciones, el estudiante puede distraerse durante las explicaciones del docente en clase.",
    //             "El estudiante ha tenido ausencias injustificadas en clases.",
    //             "El estudiante ha llegado tarde a clase sin justificación.",
    //             "El estudiante ha abandonado la clase sin permiso.",
    //             "El estudiante ha tenido dificultades para seguir las instrucciones dadas por los docentes.",
    //             "El estudiante ha mostrado falta de interés en participar activamente en las actividades escolares.",
    //             "El estudiante ha tenido dificultades para organizar su tiempo y cumplir con los plazos establecidos.",
    //             "El estudiante ha mostrado falta de responsabilidad al no completar las tareas asignadas.",
    //             "El estudiante ha tenido dificultades para seguir las indicaciones de los docentes durante las evaluaciones."
    //         ),
    //         "Comportamiento, conducta y relaciones interpersonales" => array(
    //             "Se ha observado que el estudiante podría mejorar su atención y consideración hacia los demás.",
    //             "Se ha observado que el estudiante podría mejorar su comprensión sobre el respeto y el cuidado de las instalaciones y materiales de la institución.",
    //             "A veces, el estudiante puede olvidar la importancia de garantizar que todos los estudiantes tengan la oportunidad de participar en las clases y actividades escolares.",
    //             "Es fundamental utilizar un lenguaje respetuoso y cuidadoso al interactuar con los demás.",
    //             "En ocasiones, el estudiante puede olvidar la importancia de cuidar y proteger a sus compañeros.",
    //             "A veces, el estudiante puede olvidar la importancia de tratar a todos los estudiantes con respeto y consideración.",
    //             "El estudiante ha mostrado falta de cortesía o respeto hacia los maestros o el personal.",
    //             "El estudiante ha mostrado comportamiento violento o agresivo hacia otros estudiantes o el personal.",
    //             "El estudiante ha causado daños a la propiedad de la escuela.",
    //             "El estudiante ha infringido las normas al traer armas o drogas a la escuela.",
    //             "El estudiante ha participado en actividades no permitidas por la ley en la escuela.",
    //             "El estudiante ha cometido actos de robo hacia otros estudiantes o el personal.",
    //             "El estudiante ha amenazado o intimidado a otros estudiantes o al personal.",
    //             "El estudiante ha difundido información falsa o rumores sobre otros estudiantes o el personal.",
    //             "El estudiante ha publicado contenido inapropiado o perjudicial en línea.",
    //             "El estudiante ha participado en conductas inapropiadas de naturaleza sexual en la escuela.",
    //             "Se ha observado que el estudiante podría mejorar su capacidad para trabajar en equipo y colaborar con sus compañeros.",
    //             "Se ha observado que el estudiante podría mejorar su capacidad para expresar sus ideas y opiniones de manera respetuosa.",
    //             "El estudiante ha mostrado falta de compromiso con su propio aprendizaje y desarrollo académico.",
    //             "El estudiante ha tenido dificultades para seguir las normas y reglas establecidas en la institución.",
    //             "Se ha observado que el estudiante podría mejorar su capacidad para resolver conflictos de manera pacífica y constructiva.",
    //             "El estudiante ha mostrado falta de empatía hacia las necesidades y sentimientos de sus compañeros.",
    //             "El estudiante ha tenido dificultades para adaptarse a los cambios y nuevas situaciones en el entorno escolar.",
    //             "Se ha observado que el estudiante podría mejorar su capacidad para aceptar y aprender de los errores.",
    //             "El estudiante ha mostrado falta de interés en participar en actividades extracurriculares y eventos escolares.",
    //             "El estudiante ha tenido dificultades para mantener una comunicación efectiva con sus compañeros y docentes.",
    //             "Se ha observado que el estudiante podría mejorar su capacidad para manejar el estrés y la presión académica.",
    //             "El estudiante ha mostrado falta de respeto hacia las opiniones y perspectivas diferentes a las suyas.",
    //             "Se ha observado que el estudiante podría mejorar su capacidad para aceptar y aprender de la retroalimentación recibida."
    //         ),
    //         "Compromiso con el entorno y la comunidad" => array(
    //             "El estudiante ha mostrado falta de compromiso con el cuidado y preservación del medio ambiente en la institución.",
    //             "El estudiante ha mostrado falta de interés en participar en actividades de servicio comunitario y voluntariado.",
    //             "El estudiante ha mostrado falta de interés en participar en actividades de orientación vocacional y profesional.",
    //             "El estudiante ha mostrado falta de interés en participar en actividades de promoción de la salud y bienestar.",
    //             "El estudiante ha mostrado falta de interés en participar en actividades de educación financiera y emprendimiento.",
    //             "El estudiante ha mostrado falta de interés en participar en actividades de educación cívica y ciudadanía.",
    //             "El estudiante ha mostrado falta de interés en participar en actividades de educación en derechos humanos y justicia social.",
    //             "El estudiante ha mostrado falta de interés en participar en actividades de educación en igualdad de género y empoderamiento.",
    //             "El estudiante ha mostrado falta de interés en participar en actividades de educación en prevención del bullying y la discriminación.",
    //             "El estudiante ha mostrado falta de interés en participar en actividades de educación en prevención del consumo de drogas y alcohol.",
    //             "El estudiante ha mostrado falta de interés en participar en actividades de educación en prevención de la violencia y el acoso.",
    //             "El estudiante ha mostrado falta de interés en participar en actividades de educación en prevención del bullying y la discriminación."
    //         ),
    //         "Habilidades sociales y emocionales" => array(
    //             "Se ha observado que el estudiante podría mejorar su capacidad para reconocer y valorar la diversidad cultural presente en la institución.",
    //             "Se ha observado que el estudiante podría mejorar su capacidad para aceptar y respetar las diferencias de género en la institución.",
    //             "Se ha observado que el estudiante podría mejorar su capacidad para reconocer y valorar el esfuerzo y logros de sus compañeros.",
    //             "Se ha observado que el estudiante podría mejorar su capacidad para manejar las críticas constructivas de manera adecuada.",
    //             "Se ha observado que el estudiante podría mejorar su capacidad para reconocer y valorar la diversidad de habilidades y talentos presentes en la institución.",
    //             "Se ha observado que el estudiante podría mejorar su capacidad para manejar el uso responsable de la tecnología en el entorno escolar.",
    //             "Se ha observado que el estudiante podría mejorar su capacidad para manejar el estrés y la ansiedad relacionados con el rendimiento académico.",
    //             "Se ha observado que el estudiante podría mejorar su capacidad para manejar los conflictos de manera pacífica y constructiva.",
    //             "Se ha observado que el estudiante podría mejorar su capacidad para manejar la presión social y los desafíos de la adolescencia.",
    //             "Se ha observado que el estudiante podría mejorar su capacidad para manejar los cambios emocionales y las dificultades personales.",
    //             "Se ha observado que el estudiante podría mejorar su capacidad para manejar la presión académica y las altas expectativas.",
    //             "Se ha observado que el estudiante podría mejorar su capacidad para manejar los desafíos de la transición entre etapas educativas.",
    //             "Se ha observado que el estudiante podría mejorar su capacidad para manejar las distracciones y mantener el enfoque en clase."
    //         ),
    //         "Convivencia y respeto" => array(
    //             "El estudiante ha tenido dificultades para aceptar y respetar las decisiones y autoridad de los docentes.",
    //             "El estudiante ha mostrado falta de respeto hacia las normas de convivencia establecidas en la institución.",
    //             "El estudiante ha mostrado falta de respeto hacia las opiniones y perspectivas diferentes a las suyas."
    //         )
    //     );

    //     $arr_positive = array(
    //         'Logros académicos' => array(
    //             "Obtuvo mérito académico en una prueba de matemáticas.",
    //             "Mostró un gran progreso en su rendimiento académico."
    //         ),
    //         'Participación y liderazgo' => array(
    //             "Participó activamente en actividades extracurriculares.",
    //             "Demostró habilidades de liderazgo al organizar un evento escolar.",
    //             "Ayudó a un compañero de clase con dificultades académicas."
    //         ),

    //         'Investigación y creatividad' => array(
    //             "Participó en un proyecto de investigación y presentó resultados destacados.",
    //             "Fue reconocido por su creatividad en un concurso de arte o escritura."
    //         ),
    //         'Servicio comunitario y responsabilidad social' => array(
    //             "Contribuyó de manera significativa en un proyecto comunitario.",
    //             "Participó en un programa de mentoría y tuvo un impacto positivo en el estudiante mentorado.",
    //             "Fue reconocido por su compromiso con la sostenibilidad y el cuidado del medio ambiente.",
    //             "Participó en un programa de intercambio cultural y promovió la diversidad.",
    //             "Contribuyó en la creación de un ambiente escolar inclusivo y respetuoso.",
    //             "Fue reconocido por su compromiso con el servicio comunitario y la ayuda a los demás."
    //         ),
    //         'Habilidades sociales y emocionales' => array(
    //             "Demostró habilidades de resolución de conflictos al mediar en una disputa entre compañeros.",
    //             "Mostró una actitud positiva y motivadora en el aula.",
    //             "Demostró habilidades de comunicación efectiva al presentar un proyecto frente a un público.",
    //             "Fue reconocido por su perseverancia y superación personal."
    //         ),
    //         'Deportes y actividad física' => array(
    //             "Participó en actividades deportivas y mostró un espíritu deportivo ejemplar."
    //         ),
    //         'Ciencias y tecnología' => array(
    //             "Participó en un concurso de ciencias y obtuvo un premio por su investigación."
    //         ),
    //         'Desarrollo personal' => array(
    //             "Participó en un debate escolar y presentó argumentos sólidos."
    //         ),
    //     );
    //     return
    //     [
    //         'Negativos' => $arr_negative,
    //         'Positivos' => $arr_positive,
    //     ];
    // }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function estudiant()
    {
        return $this->belongsTo(Estudiant::class);
    }

    public function profesor()
    {
        return $this->belongsTo(Profesor::class);
    }

    public function incident_reason()
    {
        return $this->belongsTo(IncidentReason::class, 'reason_id');
    }
    public function incident_agreements()
    {
        return $this->hasMany(IncidentAgreement::class);
    }

    public function getTimeAnnouncementAttribute()
    {
        if ($this->date_announcement) {
            $date = Date::parse($this->date_announcement)->format('y-m-d');
            $parse = $date . ' ' . $this->hour_announcement;
            $time = Date::parse($parse);
            return $time;
        }
    }

    public function getCodeAttribute()
    {
        return 'BE-I'.$this->id;
    }

    public static function incidents_notificated()
    {
        
    }

    public function getProfesorGuiaAttribute()
    {
        $profesors = Profesor::select('profesors.*')
            ->join('profesor_guias', 'profesors.id', '=', 'profesor_guias.profesor_id')
            ->join('seccions', 'seccions.id', '=', 'profesor_guias.seccion_id')
            ->join('inscripcions', 'seccions.id', '=', 'inscripcions.seccion_id')
            ->where('inscripcions.estudiant_id',$this->estudiant_id)
            ->first()
            ;
        return $profesors;
    }

}


/*


'estudiant_id','profesor_id','reason_id','type','description','observations','taken_actions','status_aggression','status_notify','status_active',

'estudiant_id'=>'Estudiante',
'profesor_id'=>'Profesor',
'reason_id'=>'Motivo',
'type'=>'Tipo',
'description'=>'Descripción',
'observations'=>'Observaciones del devoución',
'taken_actions'=>'Acciones tomadas',
'status_aggression'=>'Presento agresividad',
'status_notify'=>'Notificada',
'status_active'=>'Estado',

'Solicitud de servicio','Hecho ocurrido','Alta prioridad'


'estudiant_id','profesor_id','description','observations','status_active',

estudiant_id => 'Estudiante',
profesor_id => 'Profesor',
description => 'Descripción',
observations => 'Observaciones del devoución',
status_active => 'Vehículo particular',


Algunas veces el estudiante ha tenido dificultades para completar las actividades asignadas en clase dentro del plazo establecido.
A veces el estudiante puede distraerse durante las explicaciones del docente en clase.
Se ha observado que quizá el estudiante no atiende de manera efectiva al respeto y consideración hacia los demás.
Se ha observado que quizá el estudiante no ha comprendido el respeto y el cuidado de las instalaciones y materiales de la institución.
A veces el estudiante puede olvidar lo importante de que todos los estudiantes tengan la oportunidad de participar en las clases y actividades escolares.
Es importante cuidar nuestro lenguaje y ser respetuosos con los demás.
A veces el estudiante puede olvidar lo importante de que todos los estudiantes se cuiden y protejan mutuamente.
A veces el estudiante puede olvidar lo importante de que todos los estudiantes se traten con respeto y consideración.


*/
