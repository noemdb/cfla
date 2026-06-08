<?php

namespace App\Models\app\Educational;

use App\Models\app\Pescolar\Grado;
use App\Models\app\Pescolar\Seccion;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Debate extends Model
{
    use HasFactory;
    protected $fillable = [
        'competition_id',
        'token',
        'grado_id',
        'seccion_id',
        'pevaluacion_id',
        'name',
        'description',
        'question_max',
        'status_active',
        'status_empirical_evidence',
        'winner_section_id',
        'attachment',
        'context',
    ];

    const COLUMN_COMMENTS = [
        'competition_id' => 'Competición',
        'token' => 'Ident. de acceso',
        'grado_id' => 'Grado',
        'grado_full' => 'Grado/Año Sección',
        'seccion_id' => 'Sección',
        'pevaluacion_id' => 'P. Evaluación',
        'name' => 'Nombre',
        'description' => 'Descripción',
        'question_max' => 'Cantidad máxima de preguntas por categoría',
        'status_active' => 'Estado (Activo/Desactivo)',
        'status_empirical_evidence' => 'Evidencia empírica',
        'winner_section_id' => 'Sección ganadora',
        'attachment' => 'Archivo adjunto',
        'context' => 'Contexto',
        'group' => 'Cantidad de Grupos',
    ];

    const PedagogicalApproach = [
        'Constructivista',
        'Sociocultural',
        'Basado en Competencias',
        'Basado en proyectos'
    ];

    public function getActiveQuestion()
    {
        return $this->questions()->where('status_active', true)->first();
    }


    // Relación
    public function questions()
    {
        return $this->hasMany(DebateQuestion::class);
    }
    public function answers()
    {
        return $this->hasMany(DebateAnswer::class);
    }
    public function grado()
    {
        return $this->belongsTo(Grado::class);
    }
    public function seccion()
    {
        return $this->belongsTo(Seccion::class);
    }
    public function competition()
    {
        return $this->belongsTo(DebateCompetition::class, 'competition_id');
    }
    public function winnerSection()
    {
        return $this->belongsTo(Seccion::class, 'winner_section_id');
    }

    // Scope para obtener los debates activos
    public function scopeActive($query)
    {
        return $query->where('status_active', true);
    }
    // Scope para obtener los debates finalizados
    public function scopeFinished($query)
    {
        return $query->where('status_active', false);
    }

    // Accessor para obtener el nombre completo del debate
    public function getFullNameAttribute()
    {
        $grado = $this->grado->name;
        $seccion = ($this->seccion) ? $this->seccion->name : null;
        return $this->name . ' - ' . $this->competition->name . ' [' . $grado . ' ' . $seccion . ']';
    }

    public function getQuestionsUnfinishedAttribute()
    {
        $token = $this->token;
        // Obtener las preguntas del debate que no tienen respuestas
        return DebateQuestion::whereHas('debate', function ($query) use ($token) {
            $query->where('token', $token);
        })
            ->doesntHave('answers')
            ->get();
    }

    // Método para obtener el puntaje total de la sección
    public function getTotalScoreForGroup($groupId)
    {
        return $this->answers()
            ->where('group_id', $groupId)
            ->sum('score');
    }

    // Método para obtener el puntaje total de la sección
    public function getTotalScoreForSection($seccionId)
    {
        return $this->answers()
            ->where('seccion_id', $seccionId)
            ->sum('score');
    }

    public function getAttachmentUrlAttribute()
    {
        return ($this->attachment) ? asset('storage/educationals/' . $this->attachment) : null;
    }

    public static function genToken()
    {
        return substr(str_replace(['+', '/', '=', '&'], '', password_hash(bin2hex(random_bytes(45)), PASSWORD_BCRYPT)), 0, 32);
    }

    public function getUrlTokenAttribute()
    {
        return env('APP_URL') . '/general/educations/competitions/' . $this->competition->token . "/debate/" . $this->token;
    }

    public static function genTokenSm($len = 16)
    {
        $permitted_chars = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        return substr(str_shuffle($permitted_chars), 0, $len);
    }

    public function getPestudioAttribute()
    {
        return ($this->grado) ? $this->grado->pestudio : null;
    }

    public static function getPrompt($competition_id, $referents)
    {
        $competition = DebateCompetition::findOrFail($competition_id);
        $competition_info = "Nombre: {$competition->name}; Descripción: {$competition->description}; Motivo: {$competition->motive}";

        return "
        Actúa como un experto en pedagogía integradora (STEM).
        CONTEXTO EVALUATIVO:
        {$competition->context}

        COMPETICIÓN:
        {$competition_info}

        MARCO TEÓRICO ADICIONAL:
        {$referents}

        REQUERIMIENTO: Genera un objeto JSON con 5 preguntas de debate (4 opciones cada una, solo una correcta).
        Estructura exacta:
        {
            \"name\": \"Nombre del debate\",
            \"description\": \"Descripción\",
            \"questions\": [
                {
                    \"category\": \"Categoría\",
                    \"text\": \"Texto de la pregunta\",
                    \"observation\": \"Justificación\",
                    \"options\": [
                        {\"text\": \"Opción A\", \"observation\": \"Justificación\", \"status_option_correct\": true},
                        {\"text\": \"Opción B\", \"observation\": \"...\", \"status_option_correct\": false},
                        {\"text\": \"Opción C\", \"observation\": \"...\", \"status_option_correct\": false},
                        {\"text\": \"Opción D\", \"observation\": \"...\", \"status_option_correct\": false}
                    ]
                }
            ]
        }
        ";
    }

    const ARR_DAT = [
        'name' => 'El Enigma del Origen de la Vida: Un Debate Científico',
        'description' => 'Explora las teorías, evidencias y debates en torno al origen de la vida en la Tierra',
        'questions' => [
            [
                'text' => '¿Cuál de las siguientes teorías sobre el origen de la vida es la más ampliamente aceptada?',
                'observation' => 'Esta pregunta evalúa la comprensión sobre las teorías científicas',
                'category' => 'Teorías del Origen de la Vida',
                'options' => [
                    'option1' => [
                        'text' => 'Generación espontánea',
                        'observation' => 'Teoría obsoleta',
                        'status_option_correct' => ''
                    ],
                    'option2' => [
                        'text' => 'Creacionismo',
                        'observation' => 'Teoría religiosa, no científica',
                        'status_option_correct' => ''
                    ],
                    'option3' => [
                        'text' => 'Panspermia',
                        'observation' => 'Teoría que sugiere que la vida llegó desde el espacio',
                        'status_option_correct' => ''
                    ],
                    'option4' => [
                        'text' => 'Abiogénesis',
                        'observation' => 'Teoría que propone que la vida se originó a partir de materia inorgánica',
                        'status_option_correct' => 1
                    ],
                ]
            ],
            [
                'text' => '¿Qué experimento contribuyó significativamente a la creencia en la abiogénesis?',
                'observation' => 'Esta pregunta evalúa el conocimiento sobre experimentos históricos',
                'category' => 'Experimentos del Origen de la Vida',
                'options' => [
                    'option1' => [
                        'text' => 'Experimento de Louis Pasteur',
                        'observation' => 'Demostró la falsedad de la generación espontánea',
                        'status_option_correct' => ''
                    ],
                    'option2' => [
                        'text' => 'Experimento de Miller-Urey',
                        'observation' => 'Simuló condiciones primitivas y produjo moléculas orgánicas',
                        'status_option_correct' => 1
                    ],
                    'option3' => [
                        'text' => 'Experimento de Schrödinger',
                        'observation' => 'Trató sobre la física cuántica y la vida',
                        'status_option_correct' => ''
                    ],
                    'option4' => [
                        'text' => 'Experimento de Watson y Crick',
                        'observation' => 'Descubrió la estructura del ADN',
                        'status_option_correct' => ''
                    ],
                ]
            ],
            [
                'text' => '¿Qué papel juega la evolución en el origen de la vida?',
                'observation' => 'Esta pregunta evalúa la comprensión sobre el papel de la evolución',
                'category' => 'Evolución y el Origen de la Vida',
                'options' => [
                    'option1' => [
                        'text' => 'La evolución no está relacionada con el origen de la vida',
                        'observation' => 'Teoría incorrecta',
                        'status_option_correct' => ''
                    ],
                    'option2' => [
                        'text' => 'La evolución impulsó la aparición de los primeros organismos',
                        'observation' => 'Teoría correcta',
                        'status_option_correct' => 1
                    ],
                    'option3' => [
                        'text' => 'La evolución no ocurrió hasta mucho después del origen de la vida',
                        'observation' => 'Teoría incorrecta',
                        'status_option_correct' => ''
                    ],
                    'option4' => [
                        'text' => 'La evolución es responsable de la diversidad de la vida en la Tierra',
                        'observation' => 'Esta afirmación es verdadera pero no aborda directamente la relación entre la evolución y el origen de la vida',
                        'status_option_correct' => ''
                    ],
                ]
            ],
            [
                'text' => '¿Cuál de las siguientes es una implicación del origen abiótico de la vida?',
                'observation' => 'Esta pregunta evalúa la capacidad de inferir implicaciones',
                'category' => 'Implicaciones del Origen Abiotico',
                'options' => [
                    'option1' => [
                        'text' => 'La vida es un fenómeno único y aislado en el universo',
                        'observation' => 'Implicación incorrecta',
                        'status_option_correct' => ''
                    ],
                    'option2' => [
                        'text' => 'La vida puede haber surgido múltiples veces en la Tierra y en otros lugares del universo',
                        'observation' => 'Implicación correcta',
                        'status_option_correct' => 1
                    ],
                    'option3' => [
                        'text' => 'La vida fue creada por una fuerza sobrenatural',
                        'observation' => 'Implicación incorrecta',
                        'status_option_correct' => ''
                    ],
                    'option4' => [
                        'text' => 'El origen de la vida es un misterio sin resolver',
                        'observation' => 'Esta afirmación es verdadera pero no aborda directamente las implicaciones del origen abiótico',
                        'status_option_correct' => ''
                    ],
                ]
            ],
            [
                'text' => '¿Qué avance tecnológico ha tenido un impacto significativo en la comprensión del origen de la vida?',
                'observation' => 'Esta pregunta evalúa el conocimiento sobre los avances tecnológicos',
                'category' => 'Avances Tecnológicos y el Origen de la Vida',
                'options' => [
                    'option1' => [
                        'text' => 'Microscopio',
                        'observation' => 'Ha permitido observar microorganismos',
                        'status_option_correct' => ''
                    ],
                    'option2' => [
                        'text' => 'Secuenciación del genoma',
                        'observation' => 'Ha permitido comparar genomas y rastrear la evolución',
                        'status_option_correct' => 1
                    ],
                    'option3' => [
                        'text' => 'Telescopio',
                        'observation' => 'Ha permitido estudiar cuerpos celestes',
                        'status_option_correct' => ''
                    ],
                    'option4' => [
                        'text' => 'Espectroscopia',
                        'observation' => 'Ha permitido identificar y analizar compuestos químicos',
                        'status_option_correct' => ''
                    ],
                ]
            ],
        ]
    ];

    public static function list_debates($grado_id)
    {
        return Debate::where('grado_id', $grado_id)->pluck('name', 'id');
    }

    public function questionsByGrado()
    {
        return DebateQuestion::whereHas('pensum', function ($query) {
            $query->where('grado_id', $this->grado_id);
        })->where('debate_id', $this->id)->get();
    }
}
