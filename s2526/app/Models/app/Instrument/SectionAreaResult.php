<?php

namespace App\Models\app\Instrument;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SectionAreaResult extends Model
{
    use HasFactory;

    /**
     * Nombre explícito de la tabla
     */
    protected $table = 'section_area_results';

    /**
     * Atributos asignables en masa
     */
    protected $fillable = [
        'section_diagnostic_report_id',
        'subject_id',
        'area_name',
        'level_distribution',
        'precision_avg',
        'dominant_errors',
        'observation',
    ];

    /**
     * Casts de tipos
     */
    protected $casts = [
        'level_distribution' => 'array',
        'dominant_errors'    => 'array',
        'precision_avg'      => 'decimal:2',
    ];

    /**
     * =========================
     * RELACIONES
     * =========================
     */

    /**
     * Informe diagnóstico de sección al que pertenece el área
     */
    public function report()
    {
        return $this->belongsTo(
            SectionDiagnosticReport::class,
            'section_diagnostic_report_id'
        );
    }

    /**
     * Fortalezas y debilidades consolidadas del área
     */
    public function insights()
    {
        return $this->hasMany(
            SectionAreaInsight::class,
            'section_area_result_id'
        );
    }
}
