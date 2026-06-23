<?php

namespace App\Models\app\Instrument;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SectionContrast extends Model
{
    use HasFactory;

    /**
     * Nombre explícito de la tabla
     */
    protected $table = 'section_contrasts';

    /**
     * Atributos asignables en masa
     */
    protected $fillable = [
        'section_diagnostic_report_id',
        'gaps',
        'critical_subjects',
    ];

    /**
     * Casts de tipos
     */
    protected $casts = [
        'critical_subjects' => 'array',
    ];

    /**
     * =========================
     * RELACIONES
     * =========================
     */

    /**
     * Informe diagnóstico de sección al que pertenece el contraste
     */
    public function report()
    {
        return $this->belongsTo(
            SectionDiagnosticReport::class,
            'section_diagnostic_report_id'
        );
    }
}
