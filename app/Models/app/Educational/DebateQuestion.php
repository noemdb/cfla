<?php

namespace App\Models\app\Educational;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class DebateQuestion extends Model
{
    use HasFactory;
    protected $fillable = ['debate_id','category','text','time','weighting','observation','status_active','attachment','time_elapsed','status_answer'];

    const COLUMN_COMMENTS = [
        'debate_id' => 'Debates',
        'category' => 'A.Formación/Categorías',
        'text' => 'Texto de la pregunta',
        'time' => 'Tiempo [segundos]',
        'weighting' => 'Ponderación',
        'observation' => 'Observación adicional',
        'status_active' => 'Estado',
        'attachment' => 'Archivo adjunto',
        'time_elapsed' => 'Tiempo transcurrido',
        'status_answer' => 'Estado de respuesta',
    ];

    const CATEGORY = [
        '[21000] Lengua'=>'Lengua',
        '[21000] Inglés'=>'Inglés',
        '[21000] Matemática'=>'Matemática',
        '[21000] Ciencias Sociales'=>'Ciencias Sociales',
        '[21000] Ciencias Naturales y Robótica'=>'Ciencias Naturales y Robótica',
        '[21000] Estética'=>'Estética',
        '[21000] Cultura General'=>'Cultura General',
        '[21000] Educación Física'=>'Educación Física',
        '[21000] Formación Humana Cristiana'=>'Formación Humana Cristiana',
        '[21000] Robótica'=>'Robótica',
        
        '[31059] Castellano'=>'Castellano',
        '[31059] Inglés'=>'Inglés',
        '[31059] Matemáticas'=>'Matemáticas',
        '[31059] Física'=>'Física',
        '[31059] Química'=>'Química',
        '[31059] Biología'=>'Biología',
        '[31059] Ciencias Naturales'=>'Ciencias Naturales',
        '[31059] Ciencias de la tierra'=>'Ciencias de la tierra',
        '[31059] Geografía, historia y ciudadanía [GHC]'=>'Geografía, historia y ciudadanía [GHC]',
        '[31059] Formación para la Soberanía Nacional [FSN]'=>'Formación para la Soberanía Nacional [FSN]',
        '[31059] Innovación tecnológica'=>'Innovación tecnológica',
        '[31059] Robótica'=>'Robótica',
        '[31059] Informática'=>'Informática',
        '[31059] Emprendimiento'=>'Emprendimiento',
        '[31059] Educación física'=>'Educación física',
        '[31059] Arte y Patrimonio'=>'Arte y Patrimonio',
        '[31059] Seminario de investigación'=>'Seminario de investigación',
        '[31059] Orientación y convivencia'=>'Orientación y convivencia',
        '[31059] Orientación vocacional'=>'Orientación vocacional',
        '[31059] Cultura General'=>'Cultura General',
        '[31059] Craneos'=>'Craneos',
    ];

    const tailwindColors = ['gray','neutral','red','orange','yellow','green','cyan','indigo','gray','neutral','red','orange','yellow','green','cyan','indigo','gray','neutral','red','orange','yellow','green','cyan','indigo','gray','neutral','red','orange','yellow','green','cyan','indigo','gray','neutral','red','orange','yellow','green','cyan','indigo','gray','neutral','red','orange','yellow','green','cyan','indigo','gray','neutral','red','orange','yellow','green','cyan','indigo','gray','neutral','red','orange','yellow','green','cyan','indigo','gray','neutral','red','orange','yellow','green','cyan','indigo'];

    // Relación
    public function options() { return $this->hasMany(DebateOption::class,'question_id'); }
    public function answers() { return $this->hasMany(DebateAnswer::class,'question_id'); }
    public function debate() { return $this->belongsTo(Debate::class,'debate_id'); }

    public function getGradoAttribute()
    {
        return $this->debate->grado;
    }

    public function getSeccionsAttribute()
    {
        $debate = $this->debate; //dd($debate);
        $grado = $debate->grado; //dd($grado);
        // $seccions = $grado->seccions; dd($seccions);
        return $this->grado->seccions->where('status_active','true')->where('status_inscription_affects','true');
    }

    // Scope para obtener las preguntas activas
    public function scopeActive($query)
    {
        return $query->where('status_active', true);
    }
    // Scope para obtener las preguntas inactivas
    public function scopeInactive($query)
    {
        return $query->where('status_active', false);
    }

    // Accessor para obtener la respuesta correcta
    public function getOptionCorrectAttribute()
    {
        return DebateOption::where('question_id',$this->id)->where('status_option_correct',true)->first();
    }
    // Existe una respuesta correcta
    public function getExistOptionCorrectAttribute()
    {
        return ($this->option_correct) ? true : false;
    }

    public function getAttachmentUrlAttribute()
    {
        return ($this->attachment) ? asset('storage/educationals/'.$this->attachment) : null;
    }
    public function getStatusOverTimeAttribute()
    {
        return ($this->time <= $this->time_elapsed) ? true : false;
    }

    public static function getListCategories($debateId = null)
    {
        return DebateQuestion::query()
            ->join('debates', 'debates.id', '=', 'debate_questions.debate_id')
            ->when($debateId, function ($query) use ($debateId) {
                $query->where('debates.id', $debateId);
            })
            ->distinct()
            ->pluck('category');
    }

    public function getColorAttribute()
    {
        return DebateQuestion::tailwindColors[array_rand(DebateQuestion::tailwindColors)];
    }

    public function getTimeRemainingAttribute()
    {
        return $this->time - $this->time_elapsed ;
    }

    public function getStatusOptionCorrectAttribute()
    {
        return $this->time - $this->time_elapsed ;
    }

    public static function setActive($id)
    {
        DebateQuestion::query()->where('id',$id)->update(['status_active' => true]);
        DebateQuestion::query()->where('id','<>',$id)->update(['status_active' => false]);
        return DebateQuestion::find($id);
    }

    public static function setDesActive($id)
    {
        DebateQuestion::query()->where('id',$id)->update(['status_active' => false]);
        return DebateQuestion::find($id);
    }

    public static function setDesActiveAll()
    {
        DebateQuestion::query()->update(['status_active' => false]);
    }

    public static function ActiveCompetitionId($CompetitionId = null)
    {
        return DebateQuestion::query()
            ->select('debate_questions.*')
            ->join('debates', 'debates.id', '=', 'debate_questions.debate_id')
            ->where('debates.competition_id',$CompetitionId)
            ->where('debate_questions.status_active',true)
            ->orderby('debate_questions.created_at')
            ->first();
    }

    public static function list_weighting()
    {
        return DebateQuestion::query()
            ->select('debate_questions.weighting')
            // ->where('debate_questions.id',$this->id)
            ->orderby('debate_questions.weighting')
            ->distinct()
            ->pluck('weighting','weighting');
    }

    public static function list()
    {
        return DB::table('debate_questions')
            ->select('debate_questions.id')
            ->join('debates', 'debates.id', '=', 'debate_questions.debate_id')
            ->where('debate_questions.weighting','>=',100)
            ->where('debates.graod_id',8)
            ->get();
    }
}
