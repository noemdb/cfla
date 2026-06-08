<?php

namespace App\Models\app\Pescolar;

use App\Models\app\Estudiante\Boletin;
use App\Models\app\Profesor\Activity;
use App\Models\app\Profesor\Pevaluacion;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Peducativo extends Model
{
    
    use SoftDeletes;
    protected $fillable = [
        'pescolar_id','manager_id','assistant_id','deputy_id','name','description','order','status_active','show_quantitative_indicators',
        'max_number_eiplanningwks', 'max_number_eiplanningbwks', 'max_number_eiprojectks', 'max_number_eispecialks', 'max_number_eievaluationks', 'max_number_eifinalks', 
    ];
    const COLUMN_COMMENTS = [
        'pescolar_id' => 'Príodo Escolar',
        'name' => 'Nombre',
        'description' => 'Descripción',
        'order' => 'Orden de presentación',
        'status_active' => 'Estado',
        'manager_id' => 'Coordinador de Evaluación',
        'assistant_id' => 'Coordinador Asistente',
        'deputy_id' => 'Coordinador Adjunto',
        'show_quantitative_indicators' => 'Indique si se muestran o no los indicadores cuantitativos',
        'max_number_eiplanningwks'=> 'Cantidad máxima de planes semanales',
        'max_number_eiplanningbwks'=> 'Cantidad máxima de planes quincenales',
        'max_number_eiprojectks'=> 'Cantidad máxima de proyectos',
        'max_number_eispecialks'=> 'Cantidad máxima de planes especiales',
        'max_number_eievaluationks'=> 'Cantidad máxima de evaluaciones',
        'max_number_eifinalks'=> 'Cantidad máxima de Informes Pedagógico',
    ];

    public function pestudios()
    {
        return $this->hasMany('App\Models\app\Pescolar\Pestudio');
    }

    public function pescolar()
    {
        return $this->belongsTo('App\Models\app\Pescolar');
    }

    public function getStatusDeleteAttribute()
    {
        return ( empty($this->pestudios->count()) ) ? true : false ;
    }
    
    public function scopeActive($query, $flag='true') {
        return $query->where('peducativos.status_active',  $flag='true');
    }

    public function getPestudiosActive()
    {
        return $this->pestudios->where('status_active','true');
    }

    public function getProfesorsIEEsPROM($lapso_id=null)
    {
        $promedio =
            Boletin::join('evaluacions','boletins.evaluacion_id','=','evaluacions.id')
                ->join('pevaluacions','evaluacions.pevaluacion_id','=','pevaluacions.id')
                ->join('profesors','pevaluacions.profesor_id','=','profesors.id')
                ->join('pensums', 'pensums.id', '=', 'pevaluacions.pensum_id')
                ->join('pestudios', 'pestudios.id', '=', 'pensums.pestudio_id')
                ->join('peducativos', 'peducativos.id', '=', 'pestudios.peducativo_id')
                ->where('peducativos.id',$this->id)
                ->wherenull('pestudios.deleted_at')
                ->wherenull('pensums.deleted_at')
                ->wherenull('pevaluacions.deleted_at')
                ->wherenull('peducativos.deleted_at')
                ->groupBy('profesors.id')
                ->selectRaw('COUNT(*) as cnt')
                ;
        $promedio = (!empty($lapso_id)) ? $promedio->where('pevaluacions.lapso_id',$lapso_id) : $promedio  ;
        $promedio = $promedio->get()->avg('cnt'); //dd($promedio);
        return $promedio;
    }

    public static function getPeducativos($user_id) /* usada para llenar los objetos de formularios select*/
    {
        $pestudios = Peducativo::select('peducativos.*')
            ->where('peducativos.status_active','true')
            ->where(
                function($query) use ($user_id) {
                    $query->orWhere('peducativos.manager_id',$user_id)
                        ->orWhere('peducativos.assistant_id',$user_id)
                        ->orWhere('peducativos.deputy_id',$user_id)
                        ;
                })
            ->get()
            ;
        return $pestudios;
    }

    public static function list_peducativo_grado_manage($manager_id) /* usada para llenar los objetos de formularios select*/
    {
        $datas_grados = collect();
        $peducativos = Peducativo::where('manager_id',$manager_id)->active('true')->get(); 
        foreach ($peducativos as $peducativo) {
            $pestudios = $peducativo->pestudios->where('status_active','true');
            foreach ($pestudios as $pestudio) {
                $datas_grados->put($pestudio->code.'-'.$pestudio->name, $pestudio->getGradosActive()->pluck('name', 'id'));
            }
        }
        return $datas_grados;
    }

    public static function list_pestudios($user_id) /* usada para llenar los objetos de formularios select*/
    {
        $pestudios = Pestudio::select('pestudios.*')
            ->join('peducativos', 'peducativos.id', '=', 'pestudios.peducativo_id')
            ->where('pestudios.status_active','true')
            ->Where('peducativos.manager_id',$user_id)
            ->pluck('peducativos.name','peducativos.id')
            ;
        return $pestudios;
    }

    public function getPevaluacions($lapso_id=null)
    {
        $pevaluacions =
        Pevaluacion::select('pevaluacions.*','asignaturas.name as asignatura_name','grados.name as grado_name')
            ->selectRaw("CONCAT(grados.id, ' - ', grados.name, ' - ', asignaturas.name) as grado_asignatura_name")
            ->join('pensums', 'pensums.id', '=', 'pevaluacions.pensum_id')
            ->join('grados', 'grados.id', '=', 'pensums.grado_id')
            ->join('asignaturas', 'asignaturas.id', '=', 'pensums.asignatura_id')
            ->join('pestudios', 'pestudios.id', '=', 'pensums.pestudio_id')
            ->join('peducativos', 'peducativos.id', '=', 'pestudios.peducativo_id')
            ->where('peducativos.id',$this->id)
            ->where('pevaluacions.status_official',true)
            ->wherenull('pestudios.deleted_at')
            ->wherenull('pensums.deleted_at')
            ->wherenull('pevaluacions.deleted_at')
            ->groupby('pevaluacions.id');
            $pevaluacions = ($lapso_id) ? $pevaluacions->where('pevaluacions.lapso_id',$lapso_id) : $pevaluacions ;
            $pevaluacions = $pevaluacions->get();
        return $pevaluacions;
    }

    public function getActivities($lapso_id=null,$finicial=null,$ffinal=null)
    {
        $activities =
        Activity::select('activities.*')
            ->join('pevaluacions', 'pevaluacions.id', '=', 'activities.pevaluacion_id')
            ->join('pensums', 'pensums.id', '=', 'pevaluacions.pensum_id')
            ->join('grados', 'grados.id', '=', 'pensums.grado_id')
            ->join('asignaturas', 'asignaturas.id', '=', 'pensums.asignatura_id')
            ->join('pestudios', 'pestudios.id', '=', 'pensums.pestudio_id')
            ->join('peducativos', 'peducativos.id', '=', 'pestudios.peducativo_id')
            ->where('peducativos.id',$this->id)
            ->wherenull('pestudios.deleted_at')
            ->wherenull('pensums.deleted_at')
            ->wherenull('pevaluacions.deleted_at')
            ->groupby('activities.id');
            $activities = ($lapso_id) ? $activities->where('pevaluacions.lapso_id',$lapso_id) : $activities ;
            $activities = ($finicial) ? $activities->whereDate('activities.finicial','>=',$finicial) : $activities ;
            $activities = ($ffinal) ? $activities->whereDate('activities.ffinal','<=',$ffinal) : $activities ;
            $activities = $activities->get(); //dd($activities);
        return $activities;
    }

    public function getPevaluacionsWithActivitiesComplete($lapso_id=null,$finicial=null,$ffinal=null)
    {
        $pevaluacions =
            Pevaluacion::select('pevaluacions.*','asignaturas.name as asignatura_name','grados.name as grado_name')
            ->selectRaw("CONCAT(grados.id, ' - ', grados.name, ' - ', asignaturas.name) as grado_asignatura_name")
            ->join('activities', 'pevaluacions.id', '=', 'activities.pevaluacion_id')
            ->join('pensums', 'pensums.id', '=', 'pevaluacions.pensum_id')
            ->join('grados', 'grados.id', '=', 'pensums.grado_id')
            ->join('asignaturas', 'asignaturas.id', '=', 'pensums.asignatura_id')
            ->join('pestudios', 'pestudios.id', '=', 'pensums.pestudio_id')
            ->join('peducativos', 'peducativos.id', '=', 'pestudios.peducativo_id')
            ->where('peducativos.id',$this->id)
            ->where('pevaluacions.status_official',true)

            //complete
            ->whereNotNull('activities.teaching')
            ->whereNotNull('activities.learning')

            ->wherenull('pestudios.deleted_at')
            ->wherenull('pensums.deleted_at')
            ->wherenull('pevaluacions.deleted_at')
            ->groupby('pevaluacions.id');
        $pevaluacions = ($lapso_id) ? $pevaluacions->where('pevaluacions.lapso_id',$lapso_id) : $pevaluacions ;
        $pevaluacions = ($finicial) ? $pevaluacions->where('activities.finicial','>=',$finicial) : $pevaluacions ;
        $pevaluacions = ($finicial) ? $pevaluacions->where('activities.ffinal','<=',$ffinal) : $pevaluacions ;
        $pevaluacions = $pevaluacions->get();
        return $pevaluacions;
    }

    public function getGradosAttribute()
    {
        return Grado::select('grados.*')
            ->join('pestudios', 'pestudios.id', '=', 'grados.pestudio_id')
            ->join('peducativos', 'peducativos.id', '=', 'pestudios.peducativo_id')
            ->where('peducativos.id', $this->id)
            ->where('peducativos.status_active', "true")
            ->where('pestudios.status_active', "true")
            ->where('grados.status_active', "true")
            ->whereNull('peducativos.deleted_at')
            ->whereNull('pestudios.deleted_at')
            ->whereNull('grados.deleted_at')
            ->groupBy('grados.id')
            ->orderBy('grados.code_sm')
            ->get();
    }
}
