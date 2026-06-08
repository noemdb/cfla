<?php

namespace App\Models\app\Profesor\Pevaluacion;

use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\app\Estudiante\Boletin;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

use App\Models\app\Pescolar\Lapso;
use App\Models\app\Profesor\Pevaluacion;

class Evaluacion extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'pevaluacion_id','escala_id','fecha','objetivo','description','observations','status_execution'
    ];

    const COLUMN_COMMENTS = [
        'pevaluacion_id' => 'Plan de Evaluación',
        'escala_id' => 'Escala',
        'fecha' => 'Fecha',
        'objetivo' => 'Objetivo',
        'description' => 'Descripción',
        'observations' => 'Observaciones',
        'status_execution' => 'Pendiente/Ejecuta',
    ];

    public function pevaluacion()
    {
        return $this->belongsTo('App\Models\app\Profesor\Pevaluacion');
    }
    public function escala()
    {
        return $this->belongsTo('App\Models\app\Profesor\Pevaluacion\Escala');
    }
    public function boletins()
    {
        return $this->hasMany('App\Models\app\Estudiante\Boletin');
    }
    /*******************************************************************************/

    public function getStatusDeleteAttribute()
    {
        return ($this->boletins->isEmpty()) ? true : false ;
    }

    public static function getEvaluacionsManagerId($manager_id)
    {
        $evaluacions = Evaluacion::select('evaluacions.*')
        ->join('pevaluacions', 'pevaluacions.id', '=', 'evaluacions.pevaluacion_id')
        ->join('pensums', 'pensums.id', '=', 'pevaluacions.pensum_id')
        ->join('pestudios', 'pestudios.id', '=', 'pensums.pestudio_id')
        ->where('pestudios.manager_id',$manager_id)
        ->wherenull('pevaluacions.deleted_at')
        ->wherenull('pensums.deleted_at')
        ->wherenull('pestudios.deleted_at')
        ->get();
        return $evaluacions;
    }

    public function lapso($lapso_id,$estudiant_id)
    {
        $lapso =
            Lapso::select('lapsos.*')
            ->join('pevaluacions', 'pevaluacions.id', '=', 'pevaluacions.lapso_id')
            ->where('pevaluacions.id',$this->pevaluacion_id)
            ->wherenull('pevaluacions.deleted_at')
            ->first();
        return $lapso;
    }

    public function boletin_corte($lapso_id,$estudiant_id)
    {
        $lapso = Lapso::findOrFail($lapso_id);
        $date_cutnote = ($lapso->date_cutnote) ? Carbon::createFromDate($lapso->date_cutnote) : null ; //dd($date_cutnote);
        $boletin =
            Boletin::select('boletins.*')
            ->join('evaluacions', 'evaluacions.id', '=', 'boletins.evaluacion_id')
            ->where('boletins.created_at',"<=",$date_cutnote)
            ->where('evaluacions.id',$this->id)
            ->where('boletins.estudiant_id',$estudiant_id)
            ->whereNotNull('boletins.nota')
            ->whereNull('evaluacions.deleted_at')
            ->first();
            // if ($this->id == 976 && $estudiant_id==116) dd($boletin);
            //if ($boletin) if ($boletin->id == 7813) dd($boletin);
        return $boletin;
    }

    public function getPromedioAttribute()
    {
        $count =
            Boletin::select(
                DB::raw('count(boletins.id) as value'),
                DB::raw('sum(boletins.nota) as sum_nota')
            )
            ->join('evaluacions', 'evaluacions.id', '=', 'boletins.evaluacion_id')
            ->where('evaluacions.id',$this->id)
            ->wherenotnull('boletins.nota')
            ->wherenull('evaluacions.deleted_at')
            ->groupby('evaluacions.id')
            ->first();

        return ($count) ? round(($count->sum_nota/$count->value),2) : null;
    }

    public function getNotasCountAttribute()
    {
        $count = Boletin::Where('evaluacion_id',$this->id)
        ->WhereNotNull('boletins.nota')
        ->get()
        ->count();
        return $count;
    }

    public function getGradoAttribute()
    {
        return $this->pevaluacion->grado;
    }

    public function getSeccionAttribute()
    {
        return $this->pevaluacion->seccion;
    }

    public function getLapsoAttribute()
    {
        return $this->pevaluacion->lapso;
    }

    public function getAsignaturaAttribute()
    {
        return $this->pevaluacion->asignatura;
    }


    public function getFullNameAttribute()
    {
        return "[{$this->lapso->name} - {$this->grado->name} {$this->seccion->name} - {$this->asignatura->name}] {$this->description}";
    }

    public static function list_evaluacion($pevaluacion_id)
    {
        $pevaluacion = Pevaluacion::findOrFail($pevaluacion_id);
        $lapso = $pevaluacion->lapso;
        $grado = $pevaluacion->grado;
        $asignatura = $pevaluacion->asignatura;
        $evaluacions = Evaluacion::select('evaluacions.*')
            ->selectRaw("CONCAT('[',lapsos.name,' - ',grados.name,' ',seccions.name,' - ',asignaturas.name,'] ',evaluacions.description) as evaluacions_fullname")
            ->join('pevaluacions', 'pevaluacions.id', '=', 'evaluacions.pevaluacion_id')
            ->join('seccions', 'seccions.id', '=', 'pevaluacions.seccion_id')
            ->join('lapsos', 'lapsos.id', '=', 'pevaluacions.lapso_id')
            ->join('pensums', 'pensums.id', '=', 'pevaluacions.pensum_id')
            ->join('asignaturas', 'asignaturas.id', '=', 'pensums.asignatura_id')
            ->join('grados', 'grados.id', '=', 'pensums.grado_id')
            ->where('lapsos.id',$lapso->id)
            ->where('grados.id',$grado->id)
            ->where('asignaturas.id',$asignatura->id)
            ->wherenull('evaluacions.deleted_at')
            ->wherenull('pevaluacions.deleted_at')
            ->pluck('evaluacions_fullname', 'id');
        return $evaluacions;
    }


}
