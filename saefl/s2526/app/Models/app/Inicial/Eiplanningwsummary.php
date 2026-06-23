<?php

namespace App\Models\app\Inicial;

use App\Models\app\Profesor\Pevaluacion;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Eiplanningwsummary extends Model
{
    use HasFactory;

    protected $fillable = [
        'eiplanningwk_id',
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
        'eiplanningwk_id' => 'Relación con la planificación semanal',
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

    public function eiplanningwk() { return $this->belongsTo(Eiplanningwk::class, 'eiplanningwk_id');}
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