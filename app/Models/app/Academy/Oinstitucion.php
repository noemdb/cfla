<?php

namespace App\Models\app\Academy;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Oinstitucion extends Model
{
    use HasFactory;
    protected $fillable = ['code','name','description','locations','state'];

    const COLUMN_COMMENTS = [
        'code' => 'Código',
        'name' => 'Nombre',
        'description' => 'Descripción',
        'locations' => 'Localización - Municipio',
        'state' => 'Estado - E.F.',
    ];  

    public function notas()
    {
        return $this->hasMany('App\Models\app\HistoricoNota\Hnota','institucion_id');
    }

    public static function list_oinstitucions() /* usada para llenar los objetos de formularios select*/
    {
        $list_oinstitucions = Oinstitucion::select('id', 'name')
            ->pluck('name', 'name');
        return $list_oinstitucions;
    }
}
