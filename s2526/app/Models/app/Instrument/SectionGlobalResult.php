<?php

namespace App\Models\app\Instrument;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SectionGlobalResult extends Model
{
    use HasFactory;

    /**
     * Nombre explícito de la tabla
     */
    protected $table = 'section_global_results';

    /**
     * Atributos asignables en masa
     */
    protected $fillable = [
        'section_diagnostic_report_id',
        'global_summary',
        'open_ended_response_level_distribution',
        'precision_distribution',
        'total_questions_avg',
        'confidence_level',
    ];

    /**
     * Casts de tipos
     */
    protected $casts = [
        'open_ended_response_level_distribution' => 'array',
        'precision_distribution'                => 'array',
        'total_questions_avg'                   => 'decimal:2',
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
