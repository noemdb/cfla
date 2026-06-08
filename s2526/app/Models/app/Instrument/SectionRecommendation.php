<?php

namespace App\Models\app\Instrument;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SectionRecommendation extends Model
{
    use HasFactory;

    /**
     * Nombre explícito de la tabla
     */
    protected $table = 'section_recommendations';

    /**
     * Atributos asignables en masa
     */
    protected $fillable = [
        'section_diagnostic_report_id',
        'type',
        'priority',
        'recommendation',
    ];

    /**
     * Casts de tipos
     */
    protected $casts = [
        // No hay campos JSON o decimales específicos en esta migración que requieran cast
    ];

    /**
     * =========================
     * RELACIONES
     * =========================
     */

    /**
     * Informe diagnóstico de la sección
     */
    public function report()
    {
        return $this->belongsTo(
            SectionDiagnosticReport::class,
            'section_diagnostic_report_id'
        );
    }
}
