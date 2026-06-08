<?php

namespace App\Models\app\Inicial;

use App\Models\app\Profesor\Pevaluacion;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Eiprojectsummary extends Model
{
    use HasFactory;

    protected $fillable = [
        'eiprojectk_id',
        'pevaluacion_id',
        'componente',
        'objetivo',
        'aprendizaje_esperado',
        'indicadores',
        'linea_investigacion',
        'enfasis_curriculares',
        'order',
        'estrategias',
    ];

    const COLUMN_COMMENTS = [
        'eiprojectk_id' => 'Proyecto de Aula',
        'pevaluacion_id' => 'Área de aprendizaje',
        'componente' => 'Componente',
        'objetivo' => 'Objetivo',
        'aprendizaje_esperado' => 'Aprendizaje esperado',
        'indicadores' => 'Indicadores',
        'linea_investigacion' => 'Línea de investigación',
        'enfasis_curriculares' => 'Énfasis curriculares',
        'lapso_id' => 'Momento',
        'order' => 'Orden',
        'estrategias' => 'Estrategias',
    ];

    public function eiprojectk() { return $this->belongsTo(Eiprojectk::class, 'eiprojectk_id');}
    public function eiplanningwk() { return $this->belongsTo(Eiplanningwk::class, 'eiplanningwk_id');}
    public function pevaluacion() { return $this->belongsTo(Pevaluacion::class, 'pevaluacion_id');}

}
