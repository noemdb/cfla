<?php

namespace App\Models\app\Instrument;

use App\Models\app\Pescolar\Seccion;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SectionDiagnosticReport extends Model
{
    use HasFactory;

    /**
     * Nombre explícito de la tabla
     */
    protected $table = 'section_diagnostic_reports';

    /**
     * Atributos asignables en masa
     */
    protected $fillable = [
        'section_id',
        'diagnostic_id',
        'students_count',
        'global_precision_avg',
        'status',
        'source_prompt_version',
        'generated_at',
    ];

    /**
     * Casts de tipos
     */
    protected $casts = [
        'students_count'        => 'integer',
        'global_precision_avg'  => 'decimal:2',
        'generated_at'          => 'datetime',
    ];

    /**
     * =========================
     * RELACIONES
     * =========================
     */

    public function diagMain()
    {
        return $this->belongsTo(DiagMain::class, 'diagnostic_id');
    }

    /**
     * Sección académica asociada
     */
    public function section()
    {
        return $this->belongsTo(Seccion::class);
    }

    /**
     * Resultado global de la sección
     */
    public function globalResult()
    {
        return $this->hasOne(
            SectionGlobalResult::class,
            'section_diagnostic_report_id'
        );
    }

    /**
     * Resultados por área académica
     */
    public function areaResults()
    {
        return $this->hasMany(
            SectionAreaResult::class,
            'section_diagnostic_report_id'
        );
    }

    /**
     * Perfil pedagógico consolidado de la sección
     */
    public function profile()
    {
        return $this->hasOne(
            SectionProfile::class,
            'section_diagnostic_report_id'
        );
    }

    /**
     * Brechas y contrastes del grupo
     */
    public function contrast()
    {
        return $this->hasOne(
            SectionContrast::class,
            'section_diagnostic_report_id'
        );
    }

    /**
     * Recomendaciones por actor (docente, familiar, estudiante)
     */
    public function recommendations()
    {
        return $this->hasMany(
            SectionRecommendation::class,
            'section_diagnostic_report_id'
        );
    }
}
