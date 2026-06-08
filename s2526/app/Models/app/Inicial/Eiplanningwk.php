<?php

namespace App\Models\app\Inicial;

use App\Models\app\Institucion\Autoridad;
use App\Models\app\Pescolar\Grado;
use App\Models\app\Pescolar\Peducativo;
use App\Models\app\Pescolar\Profesor;
use App\Models\app\Pescolar\Seccion;
use App\Models\app\Profesor\Pevaluacion;
use App\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Eiplanningwk extends Model
{
    use HasFactory;

    protected $fillable = [
        'profesor_id',
        'grado_id',
        'seccion_id',
        'eiprojectk_id',
        'finicial',
        'ffinal',
        'tiempo_ejecucion',
        'diagnostico',
        'observacion',
    ];

    const COLUMN_COMMENTS = [
        'profesor_id' => 'Profesor',
        'grado_id' => 'Grado/Año',
        'seccion_id' => 'Sección',
        'finicial' => 'Inicio',
        'ffinal' => 'Culminación',
        'tiempo_ejecucion' => 'Cant.Semanas',
        'diagnostico' => 'Diagnóstico inicial',
        'observacion' => 'Observación',
        'lapso_id' => 'Momento',
        'eiprojectk_id' => 'Proyecto vinculado',
    ];

    public function eiplanningwsummaries() { return $this->hasMany(Eiplanningwsummary::class,'eiplanningwk_id'); }
    public function eiplanningwstrategies() { return $this->hasMany(Eiplanningwstrategy::class,'eiplanningwk_id'); }
    public function eiprojectk() { return $this->belongsTo(Eiprojectk::class,'eiprojectk_id'); }

    public function profesor() { return $this->belongsTo(Profesor::class, 'profesor_id');}
    public function grado() { return $this->belongsTo(Grado::class, 'grado_id');}
    public function seccion() { return $this->belongsTo(Seccion::class, 'seccion_id');}

    public function getPeducativoAttribute()
    {
        return Peducativo::query()
        ->select('peducativos.*')
        // ->join('peducativos', 'users.id', '=', 'peducativos.manager_id')
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


    public function getPevaluacions($profesor_id=null,$lapso_id=null)
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

        $pevaluacions = $pevaluacions->get();

        return $pevaluacions;
    }

    public function getPevaluacionsList($profesor_id=null,$lapso_id=null)
    {
        $pevaluacions = $this->getPevaluacions($profesor_id,$lapso_id);
        return ($pevaluacions->count()) ? $pevaluacions->pluck('fullname_lg','id') : [] ;
    }

    public function getOrderedStrategies()
    {
        return $this->eiplanningwstrategies()
            ->orderBy('momento_rutina_diaria') // Primero agrupar por momento de rutina
            ->orderBy('day_of_week') // Luego ordenar por día de la semana dentro de cada grupo
            ->orderByRaw('CASE WHEN `order` IS NOT NULL THEN 0 ELSE 1 END') // Priorizar registros con order definido
            ->orderBy('order') // Ordenar por el campo order (si existe)
            ->orderBy('created_at') // Orden final por fecha de creación
            ->get();
    }

    public function getOrderedSummaries()
    {
        return $this->eiplanningwsummaries()
            ->orderByRaw('CASE WHEN `order` IS NOT NULL THEN 0 ELSE 1 END') // Primero los que tienen order no nulo
            ->orderBy('order') // Luego ordenar por order (si no es nulo)
            ->orderBy('created_at') // Finalmente ordenar por created_at
            ->get();
    }

    // Agregar en el modelo Eiplanningwk
    public function getStrategyByMomentAndDay($momento_rutina_diaria, $day_of_week)
    {
        return $this->eiplanningwstrategies()
            ->where('momento_rutina_diaria', $momento_rutina_diaria)
            ->where('day_of_week', $day_of_week)
            ->first();
    }

    public function getListMomentAttribute()
    {
        return Eiplanningwstrategy::LIST_MOMENT;
    }

    public function getWeekDaysAttribute()
    {
        return Eiplanningwstrategy::WEEK_DAYS;
    }


}
