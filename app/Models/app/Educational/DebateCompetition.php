<?php

namespace App\Models\app\Educational;

use App\Models\app\Academy\Peducativo;
use App\Models\app\Academy\Pestudio;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class DebateCompetition extends Model
{
    use HasFactory;
    protected $fillable = ['user_id','name','token','description','motive','date','status_active','attachment'];

    const COLUMN_COMMENTS = [
        'user_id' => 'Usuario que creó la competición',
        'name' => 'Nombre',
        'token' => 'Ident. de acceso',
        'description' => 'Descripción',
        'motive' => 'Motivo',
        'date' => 'Fecha',
        'status_active' => 'Estado (Activo/Desactivo)',
        'attachment' => 'Archivo adjunto',
    ];

    // Relación
    public function user() { return $this->belongsTo(User::class,'user_id'); }   
    public function debates() { return $this->hasMany(Debate::class,'competition_id'); }

    // Scope para obtener las competiciones activas
    public function scopeActive($query)
    {
        return $query->where('status_active', true);
    }

    public function getAttachmentUrlAttribute()
    {
        return ($this->attachment) ? asset('storage/educationals/'.$this->attachment) : null;
    }

    public static function genToken()
    {
        return substr(str_replace(['+', '/', '=', '&'], '', bcrypt(random_bytes(45))), 0, 32);
    }

    // Método para obtener el puntaje total de la sección
    public function getPestudiosAttribute()
    {
        return Pestudio::select('pestudios.*')
            ->join('grados', 'pestudios.id', '=', 'grados.pestudio_id')
            ->join('debates', 'grados.id', '=', 'debates.grado_id')
            ->join('debate_competitions', 'debate_competitions.id', '=', 'debates.competition_id')
            ->where('debate_competitions.id', $this->id)
            ->where('pestudios.status_active', "true")
            ->where('grados.status_active', "true")
            ->groupBy('pestudios.id')
            ->get();
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

    public static function setActive($id)
    {
        $competitions = DebateCompetition::all();    
        foreach ($competitions as $competition) {
            $competition->status_active = ($competition->id == $id) ? true : false;
            $competition->save();
        } 
        
        Debate::setDesActiveAll();
        DebateQuestion::setDesActiveAll();
        return DebateCompetition::find($id);
    }    

    public static function setDesActive($id)
    {
        $competitions = DebateCompetition::all();
        foreach ($competitions as $competition) {
            $competition->status_active = ($competition->id == $id) ? false : $competition->status_active;
            $competition->save();
        }
        Debate::setDesActiveAll();
        DebateQuestion::setDesActiveAll();
        return DebateCompetition::find($id);
    }

    public static function setDesActiveAll()
    {
        $competitions = DebateCompetition::where('status_active', true)->get();
        foreach ($competitions as $competition) {
            $competition->status_active = false;
            $competition->save();
        }
    }
    
}
