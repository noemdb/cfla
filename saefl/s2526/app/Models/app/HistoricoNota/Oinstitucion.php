<?php

namespace App\Models\app\HistoricoNota;

use Illuminate\Database\Eloquent\Model;

use App\Models\app\HistoricoNota\Hnota;

class Oinstitucion extends Model
{
    protected $fillable = ['code','name','description','locations','state','status_except'];

    const COLUMN_COMMENTS = [
        'code' => 'Código',
        'name' => 'Nombre',
        'description' => 'Descripción',
        'locations' => 'Localización - Municipio',
        'state' => 'Estado - E.F.',
        'status_except' => 'Excluir de listados especiales',
    ];  

    public function notas()
    {
        return $this->hasMany('App\Models\app\HistoricoNota\Hnota','institucion_id');
    }

    public function getStatusDeleteAttribute()
    {
        $notas = Hnota::select('hnotas.*')
            ->join('oinstitucions', 'oinstitucions.id', '=', 'hnotas.institucion_id')
            ->where('hnotas.institucion_id',$this->id)
            ->get();
        // return $notas;
        return ($notas->IsEmpty()) ? true : false;
    }

    public static function list_oinstitucions($status_except = false) /* usada para llenar los objetos de formularios select*/
    {
        $list_oinstitucions = Oinstitucion::select('id', 'name');
        
        $list_oinstitucions = ($status_except) ? $list_oinstitucions->where('status_except',false) : $list_oinstitucions ;
        
        $list_oinstitucions = $list_oinstitucions->pluck('name', 'name');
        
        return $list_oinstitucions;
    }
}
