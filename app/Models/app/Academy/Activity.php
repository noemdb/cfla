<?php

namespace App\Models\app\Academy;

use App\Models\app\Academy\Lms\LmsActivityLink;
use App\Models\app\Academy\Lms\LmsActivityLog;
use App\Models\app\Academy\Lms\LmsActivityPublication;
use App\Models\app\Academy\Lms\LmsActivityResource;
use App\Models\app\Academy\Lms\LmsActivitySection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Activity extends Model implements \App\Contracts\Auditable
{
    use HasFactory;

    /**
     * Allowlist para la bitácora (Spec BINNACLE-001, ADR-005).
     * Contenido académico completo; sin campos de datos personales.
     */
    public function auditableAttributes(): array
    {
        return [
            'id', 'pevaluacion_id', 'finicial', 'ffinal', 'topic', 'thematic',
            'references', 'teaching', 'learning', 'description', 'observations',
            'comments', 'status',
        ];
    }

    public function maskedAuditFields(): array
    {
        return [];
    }

    protected $fillable = [
        'pevaluacion_id', 'finicial', 'ffinal', 'topic', 'thematic', 'references',
        'teaching', 'learning', 'description', 'observations', 'comments', 'status',
    ];

    protected $casts = [
        'status' => 'boolean',
    ];

    const COLUMN_COMMENTS = [
        'pevaluacion_id' => 'Plan de Evaluación',
        'finicial' => 'Fecha Inicial',
        'ffinal' => 'Fecha Final',
        'topic' => 'Tema generador y Énfasis',
        'thematic' => 'Tejido temático / Tema Indispensable',
        'references' => 'Referentes teórico prácticos y éticos',
        'teaching' => 'Enseñanza/Actividad Globalizada',
        'learning' => 'Aprendizaje',
        'description' => 'Actividad Evaluativa',
        'observations' => 'ODS / Sistematización',
        'comments' => 'Comentarios del Jefe de Área',
        'status' => 'Aprobación (1=Aprobado, 0=En revisión)',
    ];

    public function achievements()
    {
        return $this->hasMany(Achievement::class, 'activity_id');
    }

    public function supplement()
    {
        return $this->hasOne(ActivitySupplement::class, 'activity_id');
    }

    public function pevaluacion()
    {
        return $this->belongsTo(Pevaluacion::class, 'pevaluacion_id');
    }

    // ─── RELACIONES LMS ────────────────────────────────────────

    public function lmsPublication()
    {
        return $this->hasOne(LmsActivityPublication::class, 'activity_id');
    }

    public function lmsSections()
    {
        return $this->hasMany(LmsActivitySection::class, 'activity_id')
            ->orderBy('sort_order');
    }

    public function lmsResources()
    {
        return $this->hasMany(LmsActivityResource::class, 'activity_id')
            ->orderBy('sort_order');
    }

    public function lmsLinks()
    {
        return $this->hasMany(LmsActivityLink::class, 'activity_id')
            ->orderBy('sort_order');
    }

    public function lmsHtmlEmbeds()
    {
        return $this->hasMany(\App\Models\app\Academy\Lms\LmsHtmlEmbed::class, 'activity_id')
            ->orderBy('sort_order');
    }

    public function lmsLogs()
    {
        return $this->hasMany(LmsActivityLog::class, 'activity_id');
    }

    public function lessonReads()
    {
        return $this->hasMany(\App\Models\UserLessonRead::class, 'activity_id');
    }

    public function comments()
    {
        return $this->hasMany(\App\Models\app\Academy\Lms\ActivityComment::class, 'activity_id');
    }

    public function approvedComments()
    {
        return $this->comments()->where('is_approved', true);
    }

    /**
     * Lecciones con contenido: al menos una sección o algún recurso asociado
     * (recursos, enlaces o embeds HTML).
     *
     * Fuente única para el KPI de "Total de Lecciones" del monitor LMS y del
     * dashboard de indicadores, para que ambos midan lo mismo.
     */
    public function scopeWithLmsContent($query)
    {
        return $query->where(function ($q) {
            $q->whereHas('lmsSections')
                ->orWhereHas('lmsResources')
                ->orWhereHas('lmsLinks')
                ->orWhereHas('lmsHtmlEmbeds');
        });
    }

    public function isLmsPublished(): bool
    {
        return $this->lmsPublication?->isVisibleToStudents() ?? false;
    }

    /**
     * Indica si la actividad tiene descripción evaluativa (para el resumen).
     */
    public function getStatusResumeAttribute()
    {
        return ! empty($this->description);
    }

    /**
     * Cuenta las palabras del campo `teaching` cuya longitud sea mayor a $num letras.
     *
     * @param  int  $num  Longitud mínima (exclusiva). Por defecto 3.
     * @return int Cantidad de palabras con más de $num letras.
     */
    public function teachingWordsMayorCount(int $num = 3): int
    {
        if (empty($this->teaching)) {
            return 0;
        }

        // Normalizar: quitar caracteres no alfabéticos que no sean espacios
        $texto = preg_replace('/[^\p{L}\s]/u', '', $this->teaching);

        // Separar por espacios (uno o más)
        $palabras = preg_split('/\s+/u', trim($texto), -1, PREG_SPLIT_NO_EMPTY);

        return count(array_filter($palabras, fn (string $p) => mb_strlen($p) > $num));
    }

    /**
     * Retorna el campo `activities_avr` del Pestudio asociado a esta actividad.
     * Cadena: activity → pevaluacion → pensum → pestudio.
     */
    public function getActivitiesAvrAttribute(): ?int
    {
        $avr = optional(optional(optional($this->pevaluacion)->pensum)->pestudio)->activities_avr;

        return $avr !== null ? (int) $avr : null;
    }

    // ─── ESTRUCTURA INICIO · DESARROLLO · CIERRE ─────────────────

    /**
     * Verifica si el campo `teaching` contiene los tres marcadores de sección
     * ("INICIO:", "DESARROLLO:", "CIERRE:") en el orden esperado.
     */
    public function hasTeachingStructure(): bool
    {
        return $this->getTeachingSections() !== [];
    }

    /**
     * Descompone el campo `teaching` en tres secciones (INICIO, DESARROLLO, CIERRE).
     *
     * Solo reconoce los marcadores con dos puntos y en mayúsculas que produce
     * el formulario. Así el contenido no se corrompe cuando el texto pedagógico
     * menciona palabras sueltas como "el inicio de la jornada", "durante el
     * desarrollo" o "como cierre".
     *
     * @return array<string, string> Claves: 'INICIO', 'DESARROLLO', 'CIERRE'
     *                               Vacío si no están las tres secciones.
     */
    public function getTeachingSections(): array
    {
        $text = (string) $this->teaching;
        if (trim($text) === '') {
            return [];
        }

        $labels = ['INICIO', 'DESARROLLO', 'CIERRE'];
        $found = [];
        $cursor = 0;

        foreach ($labels as $label) {
            $pattern = '/\b'.preg_quote($label, '/').'\b\s*:\s*/u';
            if (preg_match($pattern, $text, $matches, PREG_OFFSET_CAPTURE, $cursor)) {
                $found[$label] = [
                    'marker_start' => $matches[0][1],
                    'content_start' => $matches[0][1] + strlen($matches[0][0]),
                ];
                $cursor = $found[$label]['content_start'];
            }
        }

        if (count($found) !== count($labels)) {
            return [];
        }

        $sections = [];
        foreach ($labels as $index => $label) {
            $start = $found[$label]['content_start'];
            $end = strlen($text);

            foreach (array_slice($labels, $index + 1) as $nextLabel) {
                if (isset($found[$nextLabel])) {
                    $end = $found[$nextLabel]['marker_start'];
                    break;
                }
            }

            $sections[$label] = trim(substr($text, $start, $end - $start));
        }

        return $sections;
    }
}
