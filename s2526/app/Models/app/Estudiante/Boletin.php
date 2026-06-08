<?php
namespace App\Models\app\Estudiante;

use App\Models\app\Pescolar\Baremo;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class Boletin extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'estudiant_id', 'evaluacion_id', 'nota', 'ajuste', 'user_id', 'observations',
    ];

    const COLUMN_COMMENTS = [
        'estudiant_id'  => 'Estudiante',
        'evaluacion_id' => 'Cambio de Evaluación',
        'nota'          => 'Nota (sin ajuste)',
        'profesor'      => 'Profesor',
        'evaluacion'    => 'Evaluación actual',
    ];

    public function estudiant()
    {
        return $this->belongsTo('App\Models\app\Estudiant');
    }
    public function evaluacion()
    {
        return $this->belongsTo('App\Models\app\Profesor\Pevaluacion\Evaluacion');
    }

    public function getStatusDuplicateAttribute()
    {
        $boletin = DB::table('boletins')
            ->select('boletins.*')
            ->where('boletins.estudiant_id', $this->estudiant_id)
            ->where('boletins.evaluacion_id', $this->evaluacion_id)
        // ->where('boletins.id','>',$this->id)
            ->get();
        return ($boletin->count() > 1) ? true : false;
        // return ($boletin->count() >= 1) ? true:false ;
    }

    // public function getBaremo($pestudio_id)
    // {
    //     $baremo = Baremo::select('baremos.valoracion','baremos.description')
    //         ->join('pestudios', 'pestudios.id', '=', 'baremos.pestudio_id')
    //         ->where('pestudios.id',$pestudio_id)
    //         ->Where('baremos.minimo', '<=', 5)
    //         ->Where('baremos.maxima', '>=', 5)
    //         ->first();
    //     return ($baremo) ? $baremo : null;
    // }
    // public static function getValoracion($pestudio_id,$nota)
    // {
    //     $baremo = Baremo::select('baremos.valoracion','baremos.description')
    //         ->join('pestudios', 'pestudios.id', '=', 'baremos.pestudio_id')
    //         ->where('pestudios.id',$pestudio_id)
    //         ->Where('baremos.minimo', '<=', $nota)
    //         ->Where('baremos.maxima', '>=', $nota)
    //         ->first();
    //     return $baremo;
    // }

    /* ------------------------------ */
    public function getBaremo($pestudio_id)
    {
        $lapso_id = $this->pevaluacion->lapso_id;
        return Baremo::getValoracion($pestudio_id, $this->nota, $lapso_id);
    }
    public static function getValoracion($pestudio_id, $nota, $lapso_id = null)
    {
        return Baremo::getValoracion($pestudio_id, $nota, $lapso_id);
    }
    /* ------------------------------ */

    public function getPensumAttribute()
    {
        $pensum      = null;
        $evaluacion  = ($this->evaluacion) ? $this->evaluacion : null;
        $pevaluacion = (isset($evaluacion->pevaluacion)) ? $evaluacion->pevaluacion : null;
        $pensum      = (isset($pevaluacion->pensum)) ? $pevaluacion->pensum : null;
        return $pensum;
    }

    public function getGradoAttribute()
    {
        $grado  = null;
        $pensum = (isset($this->pensum)) ? $this->pensum : null;
        $grado  = (isset($pensum->grado)) ? $pensum->grado : null;
        return $grado;
    }

    public function getPevaluacionAttribute()
    {
        $pevaluacion = null;
        $evaluacion  = ($this->evaluacion) ? $this->evaluacion : null;
        $pevaluacion = (isset($evaluacion->pevaluacion)) ? $evaluacion->pevaluacion : null;
        return $pevaluacion;
    }

    public function getSeccionAttribute()
    {
        $seccion     = null;
        $evaluacion  = ($this->evaluacion) ? $this->evaluacion : null;
        $pevaluacion = (isset($evaluacion->pevaluacion)) ? $evaluacion->pevaluacion : null;
        $seccion     = (isset($pevaluacion->seccion)) ? $pevaluacion->seccion : null;
        return $seccion;
    }

    public function getProfesorAttribute()
    {
        $profesor    = null;
        $pevaluacion = ($this->pevaluacion) ? $this->pevaluacion : null;
        $profesor    = (isset($pevaluacion->profesor)) ? $pevaluacion->profesor : null;
        return $profesor;
    }

}
