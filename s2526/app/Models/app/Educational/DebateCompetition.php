<?php

namespace App\Models\app\Educational;

use App\Models\app\Pescolar\Peducativo;
use App\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class DebateCompetition extends Model
{
    use HasFactory;
    use IndicatorTrait;
    protected $fillable = ['user_id', 'name', 'token', 'description', 'motive', 'date', 'status_active', 'attachment', 'context'];

    const COLUMN_COMMENTS = [
        'user_id' => 'Usuario que creó la competición',
        'name' => 'Nombre',
        'token' => 'Ident. de acceso',
        'description' => 'Descripción',
        'motive' => 'Motivo',
        'date' => 'Fecha',
        'status_active' => 'Estado (Activo/Desactivo)',
        'attachment' => 'Archivo adjunto',
        'context' => 'Contexto',
        'cant_group' => 'Cantidad de Grupos',
    ];

    const PROMPT = "

    ";

    // Relación
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    public function debates()
    {
        return $this->hasMany(Debate::class, 'competition_id');
    }
    public function groups()
    {
        return $this->hasMany(DebateGroup::class, 'competition_id');
    }
    public function answers()
    {
        return $this->hasMany(DebateAnswer::class, 'competition_id');
    }

    // Scope para obtener las competiciones activas
    public function scopeActive($query)
    {
        return $query->where('status_active', true);
    }

    public function getDebatesUnfinishedAttribute()
    {
        $token = $this->token;
        return Debate::whereHas('competition', function ($query) use ($token) {
            $query->where('token', $token);
        })
            ->whereHas('questions', function ($query) {
                $query->doesntHave('answers');
            })
            ->get();
    }

    public function getAttachmentUrlAttribute()
    {
        return ($this->attachment) ? asset('storage/educationals/' . $this->attachment) : null;
    }

    public static function genToken($limit = 45, $large = 32)
    {
        return substr(str_replace(['+', '/', '=', '&'], '', bcrypt(random_bytes($limit))), 0, $large);
    }

    public static function genTokenSm($len = 16)
    {
        $permitted_chars = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        return substr(str_shuffle($permitted_chars), 0, $len);
    }

    // Método para obtener el puntaje total de la sección
    public function getTotalScoreForGroup($groupId)
    {
        return $this->answers()
            ->where('group_id', $groupId)
            ->sum('score');
    }

    public static function getPrompt($string)
    {
        return
            '
        Actúa como un experto en pedagogía integradora, especialista en la metodología STEM.
        Voy a proporcionarte un listado de actividades que conforman la planificación de mis clases. Basándote en esta información, genera los datos para una única competición de debates académicos que incluya todas las actividades.

        Los recursos disponibles son: salón de clases, proyector audiovisual, pizarrón, guías interactivas y aula virtual (invertida).

        REQUERIMIENTO: Responde únicamente con un objeto JSON válido con la siguiente estructura:
        {
            "name": "Nombre de la Competición",
            "description": "Descripción de la Competición",
            "motive": "Motivo de la Competición"
        }

        Listado de actividades: ' . $string;
    }


    public static function getPromptChat($string)
    {
        return
            '
        Actúa como un experto en pedagogía integradora, especialista en la metodología STEM.
        Basándote en el listado de actividades, genera los datos para una competición de debates académicos.

        Recursos: salón de clases, proyector audiovisual, pizarrón, guías interactivas y aula virtual (invertida).

        REQUERIMIENTO: Responde únicamente con un objeto JSON válido:
        {
            "name": "Nombre de la Competición",
            "description": "Descripción de la Competición",
            "motive": "Motivo de la Competición"
        }

        Actividades: ' . $string;
    }

    public function getStringAttribute()
    {
        return 'Nombre: ' . $this->name . ';' . 'Descripción: ' . $this->description . ';' . 'Motivo: ' . $this->motive;
    }


    public function getOptionsAttribute()
    {
        return DebateOption::select('debate_options.*')
            ->join('debate_questions', 'debate_questions.id', '=', 'debate_options.question_id')
            ->join('debates', 'debates.id', '=', 'debate_questions.debate_id')
            ->join('debate_competitions', 'debate_competitions.id', '=', 'debates.competition_id')
            ->where('debate_competitions.id', $this->id)
            ->get();
    }

    public function getQuestionsAttribute()
    {
        return DebateQuestion::select('debate_questions.*')
            ->join('debates', 'debates.id', '=', 'debate_questions.debate_id')
            ->join('debate_competitions', 'debate_competitions.id', '=', 'debates.competition_id')
            ->where('debate_competitions.id', $this->id)
            ->get();
    }

    public function deleteDebates()
    {
        $debates = $this->debates;
        foreach ($debates as $debate) {
            $debate->delete();
        }
    }

    public function deleteOptions()
    {
        $options = $this->options;
        foreach ($options as $option) {
            $option->delete();
        }
    }

    public function deleteQuestions()
    {
        $questions = $this->questions;
        foreach ($questions as $question) {
            $question->delete();
        }
    }

    public function getStatusDeleteAttribute()
    {
        return ($this->answers->count() > 0) ? false : true;
    }

    public function getPeducativosAttribute()
    {
        return Peducativo::select('peducativos.*')
            ->join('pestudios', 'peducativos.id', '=', 'pestudios.peducativo_id')
            ->join('grados', 'pestudios.id', '=', 'grados.pestudio_id')
            ->join('debates', 'grados.id', '=', 'debates.grado_id')
            ->join('debate_competitions', 'debate_competitions.id', '=', 'debates.competition_id')
            ->where('debate_competitions.id', $this->id)
            ->where('peducativos.status_active', "true")
            ->where('pestudios.status_active', "true")
            ->where('grados.status_active', "true")
            ->groupBy('peducativos.id')
            ->get();
    }

    public function getTotalScoreForGrado($gradoId)
    {
        return DB::table('debate_answers')
            ->join('debate_questions', 'debate_questions.id', '=', 'debate_answers.question_id')
            ->join('debates', 'debates.id', '=', 'debate_questions.debate_id')
            ->join('debate_competitions', 'debate_competitions.id', '=', 'debates.competition_id')
            ->where('debate_competitions.id', $this->id)
            ->where('debate_answers.grado_id', $gradoId)
            ->sum('score');
    }

    // Método para obtener el puntaje total de la sección
    public function getTotalScoreForSection($seccionId)
    {
        return DB::table('debate_answers')
            ->join('debate_questions', 'debate_questions.id', '=', 'debate_answers.question_id')
            ->join('debates', 'debates.id', '=', 'debate_questions.debate_id')
            ->join('debate_competitions', 'debate_competitions.id', '=', 'debates.competition_id')
            ->where('debate_competitions.id', $this->id)
            ->where('debate_answers.seccion_id', $seccionId)
            ->sum('score');
    }

    public function getQuestionsCountByGrado()
    {
        return $this->debates()
            ->with(['grado:id,name'])
            ->withCount('questions')
            ->get()
            ->groupBy('grado_id')
            ->map(function ($debates) {
                return [
                    'id' => $debates->first()->grado->id,
                    'name' => $debates->first()->grado->name,
                    'total_questions' => $debates->sum('questions_count')
                ];
            })
            ->values();
    }
}
