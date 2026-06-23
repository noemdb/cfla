<?php

namespace App\Models\app;

use App\Models\app\RegistroTitulo\Titulo;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RegistroTitulo extends Model
{
    use SoftDeletes;

    //registro_titulo
    protected $fillable = [
        'institucion_id','pestudio_id','grado_id','tevaluacion_id','funcionario_ci','funcionario_name','name','fecha_egreso','code','tipo'
    ];
    const COLUMN_COMMENTS = ['institucion_id' => 'Institución','pestudio_id'=>'Plan de Estudio','grado_id'=>'Grado','tevaluacion_id'=>'Tipo de evaluacion',
    'funcionario_ci'=>'Cédula del funcionario gubernamental','funcionario_name'=>'Nombre del funcionario gubernamental',
    'name'=>'Nombre del Documento','fecha_egreso'=>'Fecha Egreso','code'=>'Código del Formato','tipo'=>'Tipo de Registro'];

    public function institucion()
    {
        return $this->belongsTo('App\Models\app\Institucion');
    }
    public function pestudio()
    {
        return $this->belongsTo('App\Models\app\Pescolar\Pestudio');
    }
    public function grado()
    {
        return $this->belongsTo('App\Models\app\Pescolar\Grado');
    }
    public function titulos()
    {
        return $this->hasMany('App\Models\app\RegistroTitulo\Titulo');
    }
    public function tevaluacion()
    {
        return $this->belongsTo('App\Models\app\HistoricoNota\Tevaluacion');
    }
    /*********************************************************************************************************/


    public function getStatusDeleteAttribute()
    {
        return ( empty($this->titulos->count()) ) ? true : false ;
    }

    public function getEstudiantsAttribute()
    {
        $estudiants = Estudiant::select('estudiants.*')
        ->join('titulos', 'estudiants.id', '=', 'titulos.estudiant_id')
        ->where('titulos.registro_titulo_id',$this->id)
        ->get();

        return $estudiants;
    }
    public function getTitulosOrderCI($order='asc')
    {
        $seccions = $this->grado->seccions;
        $titulos_seccions = collect();
        foreach ($seccions as $seccion) {
            $titulos = Titulo::select('titulos.*')
            ->join('registro_titulos', 'registro_titulos.id', '=', 'titulos.registro_titulo_id')
            ->join('estudiants', 'estudiants.id', '=', 'titulos.estudiant_id')
            ->join('inscripcions', 'estudiants.id', '=', 'inscripcions.estudiant_id')
            ->where('inscripcions.seccion_id',$seccion->id)
            ->where('titulos.registro_titulo_id',$this->id)
            ->orderBy('estudiants.ci_estudiant',$order)
            ->get();
            $titulos_seccions->put($seccion->name,$titulos);
        }

        return $titulos_seccions;
    }


}
