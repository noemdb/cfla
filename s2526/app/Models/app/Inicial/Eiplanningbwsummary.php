<?php

namespace App\Models\app\Inicial;

use App\Models\app\Profesor\Pevaluacion;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Eiplanningbwsummary extends Model
{
    use HasFactory;

    protected $fillable = [
        'eiplanningbwk_id',
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
        'eiplanningbwk_id' => 'Relación con la planificación semanal',
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

    public function eiplanningbwk() { return $this->belongsTo(Eiplanningbwk::class, 'eiplanningbwk_id');}
    public function pevaluacion() { return $this->belongsTo(Pevaluacion::class, 'pevaluacion_id');}
}


/*
eiplanningwk_id
pevaluacion_id
componente
objetivo
aprendizaje_esperado
indicadores
linea_investigacion
enfasis_curriculares
*/