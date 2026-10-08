<?php

namespace App\Models\app\Inicial;

use App\Models\app\Academy\Pevaluacion;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Resumen por área de aprendizaje de una planificación semanal.
 *
 * Campos: componente, objetivo, aprendizaje esperado e indicadores son la
 * Información General; línea de investigación y énfasis curriculares son
 * opcionales (regla R5 del blueprint: el runtime manda, el trait de validación
 * del legacy que los exigía se descarta).
 *
 * @property int $eiplanningwk_id
 * @property int|null $pevaluacion_id
 * @property string|null $componente
 * @property string|null $objetivo
 * @property string|null $aprendizaje_esperado
 * @property string|null $indicadores
 * @property string|null $linea_investigacion
 * @property string|null $enfasis_curriculares
 * @property int|null $order
 */
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

    public function eiplanningwk()
    {
        return $this->belongsTo(Eiplanningwk::class, 'eiplanningwk_id');
    }

    public function pevaluacion()
    {
        return $this->belongsTo(Pevaluacion::class, 'pevaluacion_id');
    }
}
