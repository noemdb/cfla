<?php

namespace App\Models\app\Academy;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Catchment extends Model
{
    use HasFactory;

    protected $fillable = [ 'group_id','token', 'firstname', 'lastname','ci_estudiant', 'grade', 'date_birth','status_foreign','country_foreign','status_siblings_college','brothers', 'gender','origin', 'representant_name', 'representant_lastname','representant_date_birth', 'representant_ci', 'email', 'relationship', 'occupation', 'educational_level', 'representant_phone','representant_cellphone', 'direction', 'reason_catholic', 'reason_interest', 'aspects_valued', 'expectations', 'importance_education', 'expectations_education', 'participation_activities', 'skills_talents', 'interests', 'challenges','status_active','status_accept_terms'];

    const COLUMN_COMMENTS = [
        'id' => 'Identificador del registro',
        'catchment_id' => 'Registros del Censo',
        'group_id' => 'Grupo',
        'token' => 'Token de acceso',
        'firstname' => 'Nombre del estudiante',
        'lastname' => 'Apellido del estudiante',
        'ci_estudiant' => 'Cédula del estudiante',
        'grade' => 'Nivel/Grado/Año escolar',
        'date_birth' => 'Fecha de nacimiento',
        'status_foreign' => '¿Procede del Extranjero?',
        'country_foreign' => 'País de procedencia',
        'status_siblings_college' => '¿Tiene hermanos estudiando en el Colegio?',
        'brothers' => 'Cantidad de hermanos estudiando en el Colegio',
        'gender' => 'Sexo',
        'origin' => 'Institución de procedencia',
        'representant_name' => 'Nombre del representante',
        'representant_lastname' => 'Apellido del representante',
        'representant_date_birth' => 'Fecha de nacimiento del representante',
        'representant_ci' => 'Cédula de Identidad',
        'email' => 'Correo electrónico',
        'relationship' => 'Parentesco con el estudiante',
        'occupation' => 'Ocupación',
        'educational_level' => 'Nivel educativo',
        'representant_phone' => 'Teléfono de contacto',
        'representant_cellphone' => 'Número de teléfono celular',
        'direction' => 'Dirección de residencia',
        'reason_catholic' => '¿Por qué está interesado en una institución escolar católica?',
        'reason_interest' => '¿Por qué está interesado en nuestra institución escolar?',
        'aspects_valued' => '¿Qué aspectos valora de nuestra institución escolar?',
        'expectations' => '¿Qué espera de nuestra institución escolar?',
        'importance_education' => '¿Qué importancia le da a la educación de su hijo/a?',
        'expectations_education' => '¿Cuáles son sus expectativas para la educación de su hijo/a?',
        'participation_activities' => '¿Está dispuesto/a a participar en las actividades de la institución escolar?',
        'skills_talents' => '¿Cuáles son las habilidades y talentos de su hijo/a?',
        'interests' => '¿Cuáles son los intereses de su representado?',
        'challenges' => '¿Cuáles son los desafíos que enfrenta su representado?',
        'status_active' => 'Estado',
        'status_accept_terms' => 'Me compromiso a participar en este todas las etapas de este proceso de matriculación',
        'status_institucion_not_found' => 'En caso de no encontrar la institución, tilde esta opción para ingresar el nombre respectivo',
    ];

    public function catchment_group() { return $this->belongsTo(CatchmentGroup::class, 'group_id'); }
    public function grado() { return $this->belongsTo(Grado::class, 'grade'); }
    public function catchmentInterviews() { return $this->hasMany(CatchmentInterview::class,'catchment_id'); }

    public function group()
    {
        return $this->belongsTo(CatchmentGroup::class, 'group_id');
    }

    // public function getActivitiesAttribute()
    // {
    //     return $this->group->activities();
    // }

    public function getActivitiesAttribute()
    {
        return CatchmentActivity::query()
        ->select('catchment_activities.*')
        ->join('catchment_groups', 'catchment_groups.id', '=', 'catchment_activities.group_id')
        ->join('catchments', 'catchment_groups.id', '=', 'catchments.group_id')
        ->leftjoin('catchment_interviews', 'catchments.id', '=', 'catchment_interviews.catchment_id')
        ->where('catchments.id',$this->id)
        // ->where('catchment_activities.status_active',true)
        ->get(); //dd($activities);
    }

    public function getFullNameAttribute()
    {
        return "{$this->firstname} {$this->lastname}";
    }

    public function getFullNameGradeAttribute()
    {
        $name = ($this->grado) ? $this->grado->name : null;
        return "{$name} - {$this->firstname} {$this->lastname}";
    }

    public function getFullGradoAttribute()
    {
        return ($this->grado) ? " {$this->grado->name} " : null;
    }

    public function getFullNameRepresentantAttribute()
    {
        return "{$this->representant_name} {$this->representant_lastname}";
    }

    public function getGroupIdEnable($grado_id)
    {
        $now = Carbon::now()->format('Y-m-d');
        $catchment_group = CatchmentGroup::query()
        ->select('catchment_groups.*')
        ->selectRaw('count(catchments.id) as count')
        ->leftjoin('catchments', 'catchment_groups.id', '=', 'catchments.group_id')
        ->join('catchment_activities', 'catchment_groups.id', '=', 'catchment_activities.group_id')
        ->where('catchment_groups.grado_id',$grado_id)
        ->where('catchment_groups.status_active',true)
        // ->where('catchment_activities.date','>',$now)
        ->groupBy('catchment_groups.id')
        ->orderBy('count','asc')
        ->first();

        if ($catchment_group) {
            // if ($catchment_group->count < $catchment_group->max) { //dd($catchment_group);
                return $catchment_group->id;
            // }
        }
    }

    public static function institutionOriginTotals($grado_id = null)
    {
        $catchments =  Catchment::select('catchments.id','catchments.origin', DB::raw('COUNT(*) AS total'))
            ->groupBy('catchments.origin')
            ->whereNotNull('catchments.grade');

        $catchments = ($grado_id) ? $catchments->where('grade',$grado_id) : $catchments ;

        $catchments = $catchments->pluck('total','origin');

        return $catchments;
    }

    public static function institutionOriginTotalsArray(Array $arr)
    {
        $catchments =  Catchment::select('catchments.id','catchments.origin', DB::raw('COUNT(*) AS total'))
            ->groupBy('catchments.origin')
            ->whereNotNull('catchments.grade');

        $catchments = (count($arr)) ? $catchments->whereIn('grade',$arr) : $catchments ;

        $catchments = $catchments->pluck('total','origin'); //dd($catchments);

        return $catchments;
    }

    public static function pestudioIdTotals()
    {
        return Catchment::select('pestudios.id','pestudios.name', DB::raw('COUNT(*) AS total'))
            ->join('grados', 'grados.id', '=', 'catchments.grade')
            ->join('pestudios', 'pestudios.id', '=', 'grados.pestudio_id')
            ->groupBy('pestudios.id')
            ->pluck('total','name');

    }

    public static function groupIdTotals()
    {
        return Catchment::select('catchment_groups.id','catchment_groups.name', DB::raw('COUNT(*) AS total'))
            ->selectRaw('CONCAT(catchment_groups.name, " <br> ", catchment_activities.date, " : ", catchment_activities.time) as fullname')
            ->join('catchment_groups', 'catchment_groups.id', '=', 'catchments.group_id')
            ->join('catchment_activities', 'catchment_groups.id', '=', 'catchment_activities.group_id')
            ->groupBy('catchment_groups.id')
            ->pluck('total','fullname');

    }

    public static function gradoIdTotals()
    {
        return Catchment::select('catchments.grade','grados.id','grados.name', DB::raw('COUNT(*) AS total'))
            ->join('grados', 'grados.id', '=', 'catchments.grade')
            ->groupBy('catchments.grade')
            ->pluck('total','name');

    }

    public static function dailyHourlyTotals()
    {
        return Catchment::selectRaw('DATE(updated_at) AS date, HOUR(updated_at) AS hour, COUNT(*) AS total')
        // ->groupBy('date', 'hour')
        ->groupBy('date')
        ->get();

    }

    public function getAgeAttribute()
    {
        return ($this->date_birth <> '0000-00-00') ? Carbon::parse($this->date_birth)->age : '-';
    }

    public function getRepresentantAgeAttribute()
    {
        return ($this->representant_date_birth <> '0000-00-00') ? Carbon::parse($this->representant_date_birth)->age : '-';
    }

    public static function getCatchmentReprogrammer($date=null,$arrGradoId=[])
    {
        $date = ($date) ? $date : Carbon::now()->format('Y-m-d');
        $catchments = Catchment::query()
        ->select('catchments.*')
        ->join('catchment_groups', 'catchment_groups.id', '=', 'catchments.group_id')
        ->join('catchment_activities', 'catchment_groups.id', '=', 'catchment_activities.group_id')
        ->leftjoin('catchment_interviews', 'catchments.id', '=', 'catchment_interviews.catchment_id')
        ->where('catchment_activities.date','<',$date)
        ->whereNull('catchment_interviews.id')
        ->whereNotNull('catchments.grade');

        $catchments = (count($arrGradoId) > 0) ? $catchments->whereNotIn('grade',$arrGradoId) : $catchments ;

        $catchments = $catchments->get();

        return $catchments;
    }

    public static function getCatchmentReprogrammerFaseOne()
    {
        return Catchment::query()
        ->select('catchments.*')
        ->leftjoin('catchment_interviews', 'catchments.id', '=', 'catchment_interviews.catchment_id')
        ->whereNull('catchment_interviews.id')
        ->whereNull('catchments.grade')
        ->get();
    }

    public static function getAccepteds()
    {
        return Catchment::query()
        ->select('catchments.*')
        ->join('catchment_interviews', 'catchments.id', '=', 'catchment_interviews.catchment_id')
        ->whereNotNull('catchments.grade')
        ->where('catchment_interviews.accepted',true)
        ->get();
    }

    public function getStatusDeleteAttribute()
    {
        return ($this->status_accepted) ? false : true ;
    }

    public function getStatusAcceptedAttribute()
    {
        $catchment = Catchment::query()
        ->select('catchments.*')
        ->join('catchment_interviews', 'catchments.id', '=', 'catchment_interviews.catchment_id')
        ->where('catchment_interviews.accepted',true)
        ->where('catchments.id',$this->id)
        ->first();
        return ($catchment) ? true : false ;
    }
}
