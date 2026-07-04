<?php

namespace App\Models\app\Inicial;

use App\Models\app\Profesor\Pevaluacion;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Eievaluationp extends Model
{
    use HasFactory;

    protected $fillable = [
        'eievaluationk_id',
        'pevaluacion_id',
        'fecha',
        'nombre_ninos',
        'aprendizaje_alcanzado',
        'componente',
        'indicadores',
        'instrumento',
        'observacion',
        'order',
    ];

    const COLUMN_COMMENTS = [
        'eievaluationk_id' => 'Plan de Evaluación',
        'pevaluacion_id' => 'Área de aprendizaje/Año',
        'fecha' => 'Fecha de evaluación',
        'nombre_ninos' => 'Nombre de los niños',
        'aprendizaje_alcanzado' => 'Aprendizaje a ser alcanzado',
        'componente' => 'Componente del área de aprendizaje',
        'indicadores' => 'Indicadores de evaluación',
        'instrumento' => 'Instrumento de evaluación',
        'observacion' => 'Observaciones adicionales del docente',
        'lapso_id' => 'Momento',
        'order' => 'Orden',
    ];

    public function eievaluationk() { return $this->belongsTo(Eievaluationk::class, 'eievaluationk_id');}
    public function pevaluacion() { return $this->belongsTo(Pevaluacion::class, 'pevaluacion_id');}
}
