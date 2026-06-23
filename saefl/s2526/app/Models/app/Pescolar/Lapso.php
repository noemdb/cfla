<?php

namespace App\Models\app\Pescolar;

use App\Models\app\Estudiant;
use Illuminate\Support\Str;

use App\Models\app\Estudiante\Boletin;
use App\Models\app\Estudiante\Inscripcion;
use App\Models\app\Pescolar\Functions\Lapso\Lists;
use Illuminate\Database\Eloquent\Model;
use App\Models\app\Profesor\Pevaluacion;
use Carbon\Carbon;

class Lapso extends Model
{
    use Lists;

    protected $dates = ['finicial', 'ffinal'];
    protected $newDateFormat = 'Y-m-d';

    protected $fillable = [
        'code',
        'code_sm',
        'name',
        'finicial',
        'ffinal',
        'academic_start_date',
        'date_cutnote',
        'date_start_census',
        'time_start_census',
        'date_end_census',
        'time_end_census',
        'date_preclosing',
        'time_preclosing',
        'status_last'
    ];

    const COLUMN_COMMENTS = [
        'code' => 'Código',
        'code_sm' => 'Abreviación',
        'name' => 'Nombre',
        'name_public' => 'Nombre Público',
        'finicial' => 'Fecha de inicio',
        'ffinal' => 'Fecha de finalización',
        'academic_start_date' => 'Fecha de inicio de actividades académicas',
        'date_cutnote' => 'Fecha del corte de nota',
        'date_start_census' => 'Fecha de inicio del censo',
        'time_start_census' => 'Hora de inicio del censo',
        'date_end_census' => 'Fecha de finalización del censo',
        'time_end_census' => 'Hora de finalización del censo',
        'date_preclosing' => 'Fecha de precierre',
        'time_preclosing' => 'Hora de precierre',
        'status_last' => 'Último lapso del período',
    ];

    public function pevaluacions()
    {
        return $this->hasMany('App\Models\app\Profesor\Pevaluacion');
    }
    public function profesor_guias()
    {
        return $this->hasMany('App\Models\app\Pescolar\ProfesorGuia');
    }
    public function ecualitativas()
    {
        return $this->hasMany('App\Models\app\Profesor\Pevaluacion\Ecualitativa');
    }
    public function edescriptivas()
    {
        return $this->hasMany('App\Models\app\Profesor\Pevaluacion\Edescriptiva');
    }

    public function getProfesorsAttribute($profesor_gestable_id)
    {
        $profesors = Profesor::select('profesors.*')
            ->join('pevaluacions', 'profesors.id', '=', 'pevaluacions.profesor_id')
            ->join('lapsos', 'lapsos.id', '=', 'pevaluacions.lapso_id')
            ->where('lapsos.id', $this->id)
            ->wherenull('pevaluacions.deleted_at')
            ->groupBy('profesors.id')
            ->get();
        return $profesors;
    }

    public function getFinicialAttribute($value)
    {
        return Carbon::parse($value)->format($this->newDateFormat);
    }
    public function getFfinalAttribute($value)
    {
        return Carbon::parse($value)->format($this->newDateFormat);
    }

    public function getStatusEnableCorte($estudiant_id)
    {
        $date = Carbon::now()->format('Y-m-d');
        $estudiant = Estudiant::find($estudiant_id);
        // $exchange_ammount_expire_bill = round($estudiant->exchange_ammount_expire_bill,2);
        // $disabled_bill = ($exchange_ammount_expire_bill<=0) ? true : false;
        $disabled_bill = $estudiant->getStatusBillDate($this->date_cutnote);
        $enabled_date = ($date >= $this->date_cutnote) ? true : false;
        return ($enabled_date && !$disabled_bill) ? true : false;
        // return ($enabled_date) ? true : false;
        // return (!$disabled_bill) ? true : false;
        //return true;
    }

    public function getStatusEnableBoletin($estudiant_id)
    {
        $date = Carbon::now()->format('Y-m-d');
        $estudiant = Estudiant::find($estudiant_id);
        // $exchange_ammount_expire_bill = round($estudiant->exchange_ammount_expire_bill,2);
        //$enabled_bill = ($exchange_ammount_expire_bill<=0) ? true : false;
        $disabled_bill = $estudiant->getStatusBillDate($this->ffinal);
        $enabled_date = ($date >= $this->ffinal) ? true : false;
        // $enabled_date = ($date >= $this->finicial && $date <= $this->ffinal) ? true : false;
        return ($enabled_date && !$disabled_bill) ? true : false;
    }

    public static function current($fecha = null)
    {
        $fecha = ($fecha) ? $fecha : Carbon::now()->format('Y-m-d');
        $lapso_first = Lapso::all()->first();
        $lapso = Lapso::whereDate('finicial', '<=', $fecha)->whereDate('ffinal', '>=', $fecha)->orderBy('id')->first();
        return ($lapso) ? $lapso : $lapso_first;
        // return $lapso;
    }

    public static function getCurrentOrFirst($fecha = null)
    {
        $fecha = ($fecha) ? $fecha : Carbon::now()->format('Y-m-d');
        $lapso_first = Lapso::all()->first();
        $lapso = Lapso::whereDate('finicial', '<=', $fecha)->whereDate('ffinal', '>=', $fecha)->orderBy('id')->first();
        return ($lapso) ? $lapso : $lapso_first;
    }

    public function getStatusActiveCensuAttribute()
    {
        $lapso = Lapso::current(); //dd($lapso);
        if ($lapso) {
            if ($lapso->date_start_census && $lapso->date_end_census && $lapso->time_start_census && $lapso->time_end_census) {
                $now = Carbon::now();
                $today = $now->format('Y-m-d');
                $dayOfWeek = $now->dayOfWeek;
                if ($dayOfWeek >= 1 && $dayOfWeek <= 5) {

                    $start = $lapso->date_start_census;
                    $end = $lapso->date_end_census;
                    if ($today >= $start && $today <= $end) {
                        $now =  Carbon::now();
                        if ($now->toTimeString() >= $lapso->time_start_census && $now->toTimeString() <= $lapso->time_end_census) {
                            return true;
                        }
                    }
                }
            }
        }
    }

    public function getStatusDeleteAttribute()
    {
        return (empty($this->profesor_guias->count()) && empty($this->pevaluacions->count())) ? true : false;
    }

    public function getGoalAsignPEAttribute()
    {
        $pensums = Pensum::all();
        $goal_asign = 0;
        foreach ($pensums as $pensum) {
            $goal_asign = $goal_asign + $pensum->grado->seccions->count();
        }
        return $goal_asign;
    }
    public function getRealAsignPEAttribute()
    {
        $real_asign_pe = Pevaluacion::where('lapso_id', $this->id)->get();

        return (empty($real_asign_pe)) ? 0 : $real_asign_pe->count();
    }

    public function getGoalCargaPEAttribute()
    {
        return $this->real_asign_p_e;
    }

    public function getRealCargaPEAttribute()
    {
        $pevaluacions = Pevaluacion::where('lapso_id', $this->id)->get();
        $real_carga_pe = 0;
        foreach ($pevaluacions as $pevaluacion) {
            if ($pevaluacion->evaluacions->count() > 0) {
                $real_carga_pe = $real_carga_pe + 1;
            }
        }
        return $real_carga_pe;
    }

    public function getGoalNotasPEAttribute()
    {
        $pevaluacions = Pevaluacion::where('lapso_id', $this->id)->get();
        $goal_notas_pe = 0;
        foreach ($pevaluacions as $pevaluacion) {
            if ($pevaluacion->evaluacions->count() > 0) {
                $goal_notas_pe  = $goal_notas_pe + $pevaluacion->goal_notas_load($this->id);
            }
        }
        return $goal_notas_pe;
    }

    public function getRealNotasPEAttribute()
    {
        $boletins = Boletin::select('boletins.*')
            ->join('evaluacions', 'evaluacions.id', '=', 'boletins.evaluacion_id')
            ->join('pevaluacions', 'pevaluacions.id', '=', 'evaluacions.pevaluacion_id')
            ->where('pevaluacions.lapso_id', $this->id)
            ->wherenotnull('boletins.nota')
            ->wherenull('evaluacions.deleted_at')
            ->wherenull('pevaluacions.deleted_at')
            ->count();
        return $boletins;
    }

    public function getBadgeAttribute()
    {
        switch ($this->id) {
            case '1':
                $class = 'info';
                break;
            case '2':
                $class = 'primary';
                break;
            case '3':
                $class = 'success';
                break;
            default:
                $class = 'secondary';
                break;
        }
        $badge = '<span class="badge badge-' . $class . ' mt-1">' . $this->id . '</span>';

        return $badge;
    }
    public function getClassAttribute()
    {
        switch ($this->id) {
            case '1':
                $class = 'primary';
                break;
            case '2':
                $class = 'success';
                break;
            case '3':
                $class = 'danger';
                break;
            default:
                $class = 'secondary';
                break;
        }
        return $class;
    }
    public function getcolorAttribute()
    {
        switch ($this->id) {
            case '1':
                $class = 'primary';
                break;
            case '2':
                $class = 'success';
                break;
            case '3':
                $class = 'danger';
                break;
            default:
                $class = 'secondary';
                break;
        }
        return $class;
    }

    public function getStatusPreclosingAttribute()
    {
        if ($this->date_preclosing) {
            if ($this->time_preclosing) {
                $now = Carbon::now()->timestamp; //dd($now);
                $date = Carbon::parse($this->date_preclosing . ' ' . $this->time_preclosing)->timestamp; //dd($now,$date,($now <= $date) ? true : false );
                return ($now <= $date) ? true : false;
            }
        }
    }

    public function getFullDatePreclosingAttribute()
    {
        if ($this->date_preclosing) {
            if ($this->time_preclosing) {
                return Carbon::parse($this->date_preclosing . ' ' . $this->time_preclosing);
            }
        }
    }

    public static function getLapsoCensusActive($fecha = null)
    {
        $fecha = ($fecha) ? $fecha : Carbon::now()->format('Y-m-d'); //dd($fecha);
        $lapso = Lapso::select('lapsos.*')
            ->where('lapsos.date_start_census', '<=', $fecha)
            ->where('lapsos.date_end_census', '>=', $fecha)
            ->first(); //dd($lapso);
        return $lapso;
    }
}


/*


*/