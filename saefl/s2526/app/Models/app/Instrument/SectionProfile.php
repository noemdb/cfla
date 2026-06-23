<?php

namespace App\Models\app\Instrument;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SectionProfile extends Model
{
    use HasFactory;

    /**
     * Nombre explícito de la tabla
     */
    protected $table = 'section_profiles';

    /**
     * Atributos asignables en masa
     */
    protected $fillable = [
        'section_diagnostic_report_id',
        'strengths',
        'needs',
        'attitudinal_factors',
        'cognitive_summary',
        'potential_barriers',
        'dominant_processing_style',
        'dominant_learning_style',
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
     * Informe diagnóstico de sección al que pertenece el perfil
     */
    public function report()
    {
        return $this->belongsTo(
            SectionDiagnosticReport::class,
            'section_diagnostic_report_id'
        );
    }
}
