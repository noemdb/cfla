<?php

namespace App\Models\app\Estudiante;

use Illuminate\Database\Eloquent\Model;
// use Illuminate\Database\Eloquent\SoftDeletes;

use App\Models\app\HistoricoNota\Hnota;
use Illuminate\Support\Facades\DB;

class GrupoEstable extends Model
{
    protected $fillable = [ 'code','code_sm','name','hour_t_week','hour_p_week','size_max','description','observations','status_belongs_ins','status_active' ];

    const COLUMN_COMMENTS = [
        'code' => 'Código',
        'code_sm' => 'Abreviatura',
        'name' => 'Nombre',
        'description' => 'Descripción',
        'hour_t_week' => 'Número de horas teóricas dictadas en la semana',
        'hour_p_week' => 'Número de horas prácticas dictadas en la semana',
        'size_max' => 'Cupos Máximos',
        'observations' => 'Observaciones',
        'status_belongs_ins' => 'Dictada en la institución',
        'status_active' => 'Estado (Activo/Inactivo)'
    ];


    public function inscripcion()
    {
        return $this->hasOne('App\Models\app\Estudiante\Inscripcion');
    }

    public function profesor_gestables()
    {
        return $this->hasMany('App\Models\app\Profesor\ProfesorGestable');
    }

    public function pestudio()
    {
        return $this->belongsTo('App\Models\app\Pescolar\Pestudio');
    }
    public function hnotas()
    {
        return $this->hasMany('App\Models\app\HistoricoNota\Hnota');
    }

    public function getNota($estudiant_id,$seccion_id,$lapso_id,$round=2)
    {
        $boletins = Boletin::select('boletins.*')
            ->join('evaluacions', 'evaluacions.id', '=', 'boletins.evaluacion_id')
            ->join('pevaluacions', 'pevaluacions.id', '=', 'evaluacions.pevaluacion_id')
            ->join('grupo_estables', 'grupo_estables.id', '=', 'pevaluacions.grupo_estable_id')
            ->where('grupo_estables.id',$this->id)

            ->where('pevaluacions.seccion_id',$seccion_id)
            ->where('pevaluacions.lapso_id',$lapso_id)
            ->where('boletins.estudiant_id',$estudiant_id)

            ->whereNotnull('boletins.nota')
            ->wherenull('pevaluacions.deleted_at')
            ->wherenull('evaluacions.deleted_at')
            ->get();
        return ($boletins->IsNotEmpty()) ? round(($boletins->sum('nota')/$boletins->count()),$round) : null;
    }

    public function getStatusDeleteAttribute()
    {
        $notas = Hnota::select('hnotas.*')
            ->join('grupo_estables', 'grupo_estables.id', '=', 'hnotas.grupo_estable_id')
            ->where('hnotas.grupo_estable_id',$this->id)
            ->get();
        return ($notas->IsEmpty()) ? true : false;
    }

    public static function list_grupo_estable_code() /* usada para llenar los objetos de formularios select*/
    {
        $list_grupo_estable_code = GrupoEstable::select('id', 'code')->orderby('name','asc')->pluck('code', 'id');

        return $list_grupo_estable_code;
    }

    public static function list_grupo_estable() /* usada para llenar los objetos de formularios select*/
    {
        $list_grupo_estable = GrupoEstable::select('id', 'name')->pluck('name', 'id');

        return $list_grupo_estable;
    }
    public static function list_grupo_estable_full() /* usada para llenar los objetos de formularios select*/
    {
        $list_grupo_estable = GrupoEstable::select('id', 'name',DB::raw("CONCAT(name,' || ',code) as fullname"))->orderby('name','asc')->pluck('fullname', 'id');

        return $list_grupo_estable;
    }

    public static function list_grupo_estable_full_inscriptions() /* usada para llenar los objetos de formularios select*/
    {
        $list = GrupoEstable::select('grupo_estables.*', 'grupo_estables.name',DB::raw("CONCAT(grupo_estables.name,' || ',grupo_estables.code) as fullname"))
        ->join('inscripcions', 'grupo_estables.id', '=', 'inscripcions.grupo_estable_id')
        ->wherenull('inscripcions.deleted_at')
        ->orderby('grupo_estables.name','asc')
        ->pluck('fullname', 'grupo_estables.id');

        return $list;
    }

    public static function list_grupo_estable_active() /* usada para llenar los objetos de formularios select*/
    {
        $list_grupo_estable = GrupoEstable::select('id', 'name',DB::raw("CONCAT(name,' || ',code) as fullname"))->where('status_active','true')->orderby('name','asc')->pluck('fullname', 'id');

        return $list_grupo_estable;
    }

}
