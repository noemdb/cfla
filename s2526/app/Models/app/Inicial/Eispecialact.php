<?php

namespace App\Models\app\Inicial;

use App\Models\app\Profesor\Pevaluacion;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Eispecialact extends Model
{
    use HasFactory;
    protected $fillable = [
        'eispecialk_id',
        'pevaluacion_id',
        'componente',
        'objetivo',
        'aprendizaje_esperado',
        'indicadores',
        'linea_investigacion',
        'enfasis_curriculares',
        'order',
    ];

    const COLUMN_COMMENTS = [
        'eispecialk_id' => 'Plan Especial',
        'pevaluacion_id' => 'Área de aprendizaje',
        'componente' => 'Componente',
        'objetivo' => 'Objetivo',
        'aprendizaje_esperado' => 'Aprendizaje esperado',
        'indicadores' => 'Indicadores',
        'linea_investigacion' => 'Línea de investigación',
        'enfasis_curriculares' => 'Énfasis curriculares',
        'lapso_id' => 'Momento',
        'order' => 'Orden',
    ];

    public function eispecialk() { return $this->belongsTo(Eispecialk::class, 'eispecialk_id');}
    public function pevaluacion() { return $this->belongsTo(Pevaluacion::class, 'pevaluacion_id');}
}
