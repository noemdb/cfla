<?php

namespace App\Models\app\Inicial;

use App\Models\app\Academy\Pevaluacion;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Resumen por área de aprendizaje de un proyecto de aula.
 *
 * OJO al esquema: a diferencia de los resúmenes de planificación, aquí
 * `pevaluacion_id` es NOT NULL y `componente`, `objetivo`,
 * `linea_investigacion` y `enfasis_curriculares` son `varchar(191)`
 * (texto corto), no `text`. Sí tiene columna `estrategias`.
 *
 * El legacy declaraba aquí una relación `eiplanningwk()` que apuntaba a una
 * columna `eiplanningwk_id` INEXISTE en la tabla: se omite a propósito
 * (relación rota, blueprint/inicial · §B.3.2).
 *
 * @property int $eiprojectk_id
 * @property int $pevaluacion_id
 * @property string|null $componente
 * @property string|null $objetivo
 * @property string|null $aprendizaje_esperado
 * @property string|null $indicadores
 * @property string|null $linea_investigacion
 * @property string|null $enfasis_curriculares
 * @property int|null $order
 * @property string|null $estrategias
 */
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

    public function eiprojectk()
    {
        return $this->belongsTo(Eiprojectk::class, 'eiprojectk_id');
    }

    public function pevaluacion()
    {
        return $this->belongsTo(Pevaluacion::class, 'pevaluacion_id');
    }
}
