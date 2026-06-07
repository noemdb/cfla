<?php

namespace App\Models\app\Academy;

use App\Models\app\Academy\Pevaluacion;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Activity extends Model
{
    use HasFactory;

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

    public function pevaluacion()
    {
        return $this->belongsTo(Pevaluacion::class, 'pevaluacion_id');
    }

    /**
     * Indica si la actividad tiene descripción evaluativa (para el resumen).
     */
    public function getStatusResumeAttribute()
    {
        return !empty($this->description);
    }

    /**
     * Cuenta las palabras del campo `teaching` cuya longitud sea mayor a $num letras.
     *
     * @param  int  $num  Longitud mínima (exclusiva). Por defecto 3.
     * @return int        Cantidad de palabras con más de $num letras.
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

        return count(array_filter($palabras, fn(string $p) => mb_strlen($p) > $num));
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
     * Verifica si el campo `teaching` contiene las tres palabras clave:
     * INICIO, DESARROLLO y CIERRE.
     */
    public function hasTeachingStructure(): bool
    {
        if (empty($this->teaching)) {
            return false;
        }
        foreach (['INICIO', 'DESARROLLO', 'CIERRE'] as $kw) {
            if (!preg_match('/\b' . preg_quote($kw, '/') . '\b/ui', $this->teaching)) {
                return false;
            }
        }
        return true;
    }

    /**
     * Descompone el campo `teaching` en tres secciones (INICIO, DESARROLLO, CIERRE).
     *
     * @return array<string, string>  Claves: 'INICIO', 'DESARROLLO', 'CIERRE'
     *                                Vacío si no están las tres palabras.
     */
    public function getTeachingSections(): array
    {
        if (!$this->hasTeachingStructure()) {
            return [];
        }

        // Partir por las palabras clave, capturándolas como delimitadores
        $pattern = '/\b(INICIO|DESARROLLO|CIERRE)\b\s*:?\s*/ui';
        $parts = preg_split($pattern, $this->teaching, -1, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE);

        $sections = [];
        $currentLabel = null;
        $preamble = '';

        foreach ($parts as $part) {
            $upper = mb_strtoupper($part);
            if (in_array($upper, ['INICIO', 'DESARROLLO', 'CIERRE'])) {
                if ($currentLabel !== null && !isset($sections[$currentLabel])) {
                    $sections[$currentLabel] = '';
                }
                $currentLabel = $upper;
                $sections[$currentLabel] = '';
            } else {
                if ($currentLabel === null) {
                    $preamble .= $part;
                } else {
                    $sections[$currentLabel] .= $part;
                }
            }
        }

        // Si hay texto antes del primer INICIO, anteponerlo a la sección INICIO
        if (trim($preamble) !== '' && isset($sections['INICIO'])) {
            $sections['INICIO'] = trim($preamble) . "\n" . trim($sections['INICIO']);
        }

        return array_map('trim', $sections);
    }
}
