<?php

namespace App\Models\app\Inicial;

use App\Models\app\Pescolar\Grado;
use App\Models\app\Pescolar\Lapso;
use App\Models\app\Pescolar\Peducativo;
use App\Models\app\Pescolar\Profesor;
use App\Models\app\Pescolar\Seccion;
use App\Models\app\Profesor\Pevaluacion;
use App\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Eievaluationk extends Model
{
    use HasFactory;

    protected $fillable = ['profesor_id','grado_id','lapso_id','seccion_id','finicial','ffinal','observaciones','recomendacion','asistencia'];
    protected $dates = ['finicial','ffinal'];

    const COLUMN_COMMENTS = [
        'profesor_id' => 'Profesor',
        'grado_id' => 'Grado',
        'lapso_id' => 'Momento',
        'seccion_id' => 'Sección',
        'finicial' => 'Fecha inicial',
        'ffinal' => 'Fecha final',
        'observaciones' => 'Observaciones del docente',
        'recomendacion' => 'Recomendación del Coord. de Evaluación',
        'asistencia' => 'Control de Asistencia',
        ////////////////////////////////////////
        'tiempo_ejecucion' => 'Período de ejecucion',
    ];

    public function eievaluationps() { return $this->hasMany(Eievaluationp::class,'eievaluationk_id'); }
    public function profesor() { return $this->belongsTo(Profesor::class, 'profesor_id');}
    public function grado() { return $this->belongsTo(Grado::class, 'grado_id');}
    public function seccion() { return $this->belongsTo(Seccion::class, 'seccion_id');}
    public function lapso() { return $this->belongsTo(Lapso::class, 'lapso_id');}

    public function getPeducativoAttribute()
    {
        return Peducativo::query()
        ->select('peducativos.*')
        ->join('pestudios', 'peducativos.id', '=', 'pestudios.peducativo_id')
        ->join('grados', 'pestudios.id', '=', 'grados.pestudio_id')
        ->where('grados.id',$this->grado_id)
        ->groupBy('peducativos.manager_id')
        ->orderBy('peducativos.id')
        ->first();
    }

    public function getManagerAttribute()
    {
        return User::query()
        ->select('users.*')
        ->join('peducativos', 'users.id', '=', 'peducativos.manager_id')
        ->join('pestudios', 'peducativos.id', '=', 'pestudios.peducativo_id')
        ->join('grados', 'pestudios.id', '=', 'grados.pestudio_id')
        ->where('grados.id',$this->grado_id)
        ->groupBy('peducativos.manager_id')
        ->orderBy('peducativos.id')
        ->first();
    }

    public function getPevaluacions($profesor_id=null,$lapso_id=null) //{$this->asignatura->code} {$grado_code} {$seccion_name} {$lapso_code_sm}
    {
        $pevaluacions = Pevaluacion::select('pevaluacions.*')
        ->selectRaw('CONCAT(asignaturas.name, " [",asignaturas.code, "] ",grados.code, " ",seccions.name, " ", lapsos.code_sm) as fullname_lg')
        ->join('pensums', 'pensums.id', '=', 'pevaluacions.pensum_id')
        ->join('asignaturas', 'asignaturas.id', '=', 'pensums.asignatura_id')
        ->join('grados', 'grados.id', '=', 'pensums.grado_id')
        ->join('seccions', 'seccions.id', '=', 'pevaluacions.seccion_id')
        ->join('lapsos', 'lapsos.id', '=', 'pevaluacions.lapso_id')

        ->where('seccions.id',$this->seccion_id)

        ->wherenull('pensums.deleted_at')
        ->wherenull('pevaluacions.deleted_at');

        $pevaluacions = ($profesor_id) ? $pevaluacions->where('pevaluacions.profesor_id',$profesor_id) : $pevaluacions ;
        $pevaluacions = ($lapso_id) ? $pevaluacions->where('pevaluacions.lapso_id',$lapso_id) : $pevaluacions ;

        $pevaluacions = $pevaluacions->get(); //dd($pevaluacions);

        return $pevaluacions;
    }

    public function getPositionsForArea($id)
    {
        return Eievaluationp::select('eievaluationps.*')
            ->join('eievaluationks', 'eievaluationks.id', '=', 'eievaluationps.eievaluationk_id')
            ->join('pevaluacions', 'pevaluacions.id', '=', 'eievaluationps.pevaluacion_id')
            ->where('eievaluationks.id', $this->id)
            ->where('pevaluacions.id', $id)
            ->orderByRaw('CASE WHEN `eievaluationps`.`order` IS NOT NULL THEN 0 ELSE 1 END') // Primero los que tienen order no nulo
            ->orderBy('eievaluationps.order') // Luego ordenar por order (si no es nulo)
            ->orderBy('eievaluationps.created_at') // Finalmente ordenar por created_at
            ->get();
    }

    public function getPositionsForAreaFilter($id)
    {
        return Eievaluationp::where('eievaluationk_id', $this->id)
        ->where('pevaluacion_id', $id)
        ->where(function($query) {
            $query->whereNotNull('fecha')
                ->orWhereNotNull('nombre_ninos')
                ->orWhereNotNull('aprendizaje_alcanzado')
                ->orWhereNotNull('indicadores')
                ->orWhereNotNull('instrumento')
                ->orWhereNotNull('observacion');
        })
        ->orderByRaw('CASE WHEN `order` IS NOT NULL THEN 0 ELSE 1 END')
        ->orderBy('order')
        ->orderBy('created_at')
        ->get();
    }

    public function getPevaluacionsList($profesor_id=null,$lapso_id=null)
    {
        $pevaluacions = $this->getPevaluacions($profesor_id,$lapso_id);
        return ($pevaluacions->count()) ? $pevaluacions->pluck('fullname_lg','id') : [] ;
    }

    public function getOrderedEvaluationps()
    {
        return $this->eievaluationps()
            ->orderByRaw('CASE WHEN `order` IS NOT NULL THEN 0 ELSE 1 END') // Primero los que tienen order no nulo
            ->orderBy('order') // Luego ordenar por order (si no es nulo)
            ->orderBy('created_at') // Finalmente ordenar por created_at
            ->get();
    }
}
