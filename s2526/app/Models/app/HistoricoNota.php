<?php

namespace App\Models\app;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use App\Models\app\HistoricoNota\Hnota;
use App\Models\app\HistoricoNota\Oinstitucion;
use App\Models\app\Pescolar\Pestudio;
use App\Models\app\Pescolar\Pensum;
use Illuminate\Support\Facades\DB;

class HistoricoNota extends Model
{
    use SoftDeletes;
    protected $dates = ['fecha_expedicion'];
    protected $fillable = [
        'pestudio_id','estudiant_id','description','observations','fecha_expedicion','deleted_at'
    ];
    /*********************************************************************/
    public function pestudio()
    {
        return $this->belongsTo('App\Models\app\Pescolar\Pestudio');
    }
    public function estudiant()
    {
        return $this->belongsTo('App\Models\app\Estudiant');
    }
    public function hnotas()
    {
        return $this->hasMany('App\Models\app\HistoricoNota\Hnota');
    }
    /*********************************************************************/

    public function getOinstitucionsAttribute()
    {
        $oinstitucions = DB::table('oinstitucions')->select('oinstitucions.*')
            ->join('hnotas', 'oinstitucions.id', '=', 'hnotas.institucion_id')
            ->where('hnotas.estudiant_id',$this->estudiant_id)
            ->WhereNull('hnotas.deleted_at')
            ->groupby('oinstitucions.id')
            ->get();
        return $oinstitucions;
    }

    public function getHNota($pensum_id)
    {
        $nota = Hnota::select('hnotas.*')
            ->withTrashed()
            ->join('estudiants', 'estudiants.id', '=', 'hnotas.estudiant_id')
            ->join('pensums', 'pensums.id', '=', 'hnotas.pensum_id')
            ->where('hnotas.estudiant_id',$this->estudiant_id)
            ->where('hnotas.pensum_id',$pensum_id)
            ->WhereNull('estudiants.deleted_at')
            ->WhereNull('pensums.deleted_at')
            ->first();
        return $nota;
    }
    
    public function getRealNotas($pestudio_id)
    {
        $estudiant = $this->estudiant;
        $grado_id = (!empty($estudiant->inscripcion->seccion->grado->id)) ? $estudiant->inscripcion->seccion->grado->id:0;

        $hnotas = Hnota::select('hnotas.*')
            ->withTrashed()
            ->join('estudiants', 'estudiants.id', '=', 'hnotas.estudiant_id')
            ->join('pensums', 'pensums.id', '=', 'hnotas.pensum_id')
            ->join('grados', 'grados.id', '=', 'pensums.grado_id')
            ->join('asignaturas', 'asignaturas.id', '=', 'pensums.asignatura_id')
            ->where('hnotas.estudiant_id',$this->estudiant_id)
            ->where('grados.id','<',$grado_id)
            ->where('pensums.pestudio_id',$pestudio_id)
            ->WhereNull('estudiants.deleted_at')
            ->WhereNull('pensums.deleted_at')
            ->WhereNull('grados.deleted_at')
            ->WhereNull('asignaturas.deleted_at')
            ->where(function ($query) {
                $query->where('hnotas.valor', '<>', null)
                      ->orWhere('hnotas.literal', '<>', null);
            })
            ->get(); //dd($hnotas);
        return ($hnotas) ? $hnotas->count() : null;
    }

    public function getGoalNotas($pestudio_id)
    {
        $estudiant = $this->estudiant;
        $grado_id = (!empty($estudiant->inscripcion->seccion->grado->id)) ? $estudiant->inscripcion->seccion->grado->id:0;

        $pensums = Pensum::select('pensums.*')
            ->join('grados', 'grados.id', '=', 'pensums.grado_id')
            ->join('asignaturas', 'asignaturas.id', '=', 'pensums.asignatura_id')
            ->where('pensums.pestudio_id',$pestudio_id)
            ->where('grados.id','<',$grado_id)
            ->WhereNull('pensums.deleted_at')
            ->WhereNull('grados.deleted_at')
            ->WhereNull('asignaturas.deleted_at')
            ->get();
        return ($pensums) ? $pensums->count() : null;
    }

}
