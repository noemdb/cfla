<?php

namespace App\Models\app\HistoricoNota;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Hnota extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'pensum_id','grupo_estable_id','estudiant_id','historico_nota_id','tevaluacion_id',
        'institucion_id','valor','literal','tipo','fecha','description','observations','user_id','deleted_at'
    ];
    /*********************************************************************/
    public function estudiant()
    {
        return $this->belongsTo('App\Models\app\Estudiant');
    }
    public function historico_nota()
    {
        return $this->belongsTo('App\Models\app\HistoricoNota');
    }
    public function pensum()
    {
        return $this->belongsTo('App\Models\app\Pescolar\Pensum');
    }
    public function grupo_estable()
    {
        return $this->belongsTo('App\Models\app\Estudiante\GrupoEstable');
    }
    
    public function tevaluacion()
    {
        return $this->belongsTo('App\Models\app\HistoricoNota\Tevaluacion');
    }
    public function oinstitucion()
    {
        return $this->belongsTo('App\Models\app\HistoricoNota\Oinstitucion','institucion_id');
    }

    public function fixNotasUnique()
    {
        return Hnota::where('id', '!=', $this->id)->where('pensum_id',$this->pensum_id)->where('estudiant_id',$this->estudiant_id)->delete();
    }

}
