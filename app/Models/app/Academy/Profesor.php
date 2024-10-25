<?php

namespace App\Models\app\Academy;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Profesor extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id','ti_teacher','ci_profesor','lastname','name','gender','date_birth','city_birth','town_hall_birth',
        'state_birth','country_birth','dir_address','phone','cellphone','email','gsemail','gspassword','status_census_taker','status_active'
    ];

    public static function getProfesorsAcademic()
    {
        return DB::table('profesors')
        ->select('profesors.id','profesors.ci_profesor', 'profesors.name', 'profesors.lastname','profesors.date_birth', /* Otras columnas necesarias */)
        // ->selectRaw('count(pevaluacions.id) as count_pevaluacions')
        ->join('pevaluacions', 'profesors.id', '=', 'pevaluacions.profesor_id')
        ->where('profesors.status_active', "true")
        ->wherenull('pevaluacions.deleted_at')
        ->orderBy('profesors.name')
        ->groupBy('profesors.id','profesors.ci_profesor','profesors.name', 'profesors.lastname','profesors.date_birth')
        ->get();
    }
    
}
