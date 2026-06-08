<?php

namespace App\Models\app\Pescolar;

use Illuminate\Database\Eloquent\Model;

class Prosecucion extends Model
{
    protected $fillable = [
        'seccion_id','estudiant_id','observations'
    ];

    public function estudiant()
    {
        return $this->belongsTo('App\Models\app\Estudiant','estudiant_id');
    }

    public function seccion()
    {
        return $this->belongsTo('App\Models\app\Pescolar\Seccion');
    }

    public function getFullNameAttribute()
    {
        $seccion = (!empty($this->seccion->id)) ? $this->seccion->name : null ;
        $grado = (!empty($this->seccion->grado->id)) ? $this->seccion->grado->name : null ;
        return "{$grado} {$seccion}";
    }

    public static function list_prosecucion() /* usada para llenar los objetos de formularios select */
    {
        $grados = Grado::active('true')->get();
        $datas_seccions = collect();
        foreach ($grados as $grado) {
            foreach ($grado->seccions as $seccion) {
                $name = $grado->name .' ' . $seccion->name;
                $datas_seccions->put($seccion->id, $name);
            }
        }
        return $datas_seccions;
    }
}
