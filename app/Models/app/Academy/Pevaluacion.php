<?php

namespace App\Models\app\Academy;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class Pevaluacion extends Model implements \App\Contracts\Auditable
{
    use HasFactory, SoftDeletes;

    /**
     * Allowlist para la bitácora (Spec BINNACLE-001, ADR-005).
     */
    public function auditableAttributes(): array
    {
        return [
            'id', 'profesor_id', 'lapso_id', 'seccion_id', 'pensum_id', 'grupo_estable_id',
            'status_baremo', 'status_official', 'status_note_report', 'nota_type',
            'escala_id', 'objetivo', 'description', 'observations', 'category',
        ];
    }

    public function maskedAuditFields(): array
    {
        return [];
    }

    protected $fillable = [
        'profesor_id', 'lapso_id', 'seccion_id', 'pensum_id', 'grupo_estable_id',
        'status_baremo', 'status_official', 'status_note_report', 'nota_type',
        'escala_id', 'objetivo', 'description', 'observations', 'category',
    ];

    /**
     * Valores por defecto alineados al esquema (evita inserts con NULL en
     * columnas NOT NULL cuando la creación no los especifica, p. ej.
     * Planning\Pevaluacion\IndexComponent::save()).
     */
    protected $attributes = [
        'status_baremo' => 'true',
        'status_official' => '1',
        'status_note_report' => '1',
        'escala_id' => 1,
        'nota_type' => 'PROMEDIADA',
    ];

    protected $casts = [
        'status_official' => 'boolean',
        'status_note_report' => 'boolean',
    ];

    protected $table = 'pevaluacions';

    const COLUMN_COMMENTS = [
        'profesor_id' => 'Profesor',
        'lapso_id' => 'Momento',
        'seccion_id' => 'Sección',
        'pensum_id' => 'Área de Formación',
        'grupo_estable_id' => 'Grupo Estable',
        'status_baremo' => 'Baremo',
        'status_official' => 'En documentos oficiales',
        'nota_type' => 'Tipo de nota',
        'escala_id' => 'Escala',
        'objetivo' => 'Objetivo',
        'description' => 'Descripción',
        'observations' => 'Observaciones',
        'category' => 'Category',
        'status_note_report' => 'En Informe de Notas',
    ];

    // ─── RELACIONES ──────────────────────────────────────────────

    public function profesor()
    {
        return $this->belongsTo(Profesor::class, 'profesor_id');
    }

    public function lapso()
    {
        return $this->belongsTo(Lapso::class, 'lapso_id');
    }

    public function seccion()
    {
        return $this->belongsTo(Seccion::class, 'seccion_id');
    }

    public function pensum()
    {
        return $this->belongsTo(Pensum::class, 'pensum_id');
    }

    public function grado()
    {
        return $this->hasOneThrough(
            Grado::class,
            Seccion::class,
            'id',
            'id',
            'seccion_id',
            'grado_id'
        );
    }

    public function pestudio()
    {
        return $this->hasOneThrough(
            Pestudio::class,
            Pensum::class,
            'id',
            'id',
            'pensum_id',
            'pestudio_id'
        );
    }

    public function activities()
    {
        return $this->hasMany(Activity::class, 'pevaluacion_id');
    }

    public function grupoEstable()
    {
        return $this->belongsTo(GrupoEstable::class, 'grupo_estable_id');
    }

    public function evaluacions()
    {
        return $this->hasMany(Evaluacion::class, 'pevaluacion_id');
    }

    /**
     * Informes finales de Educación Inicial emitidos sobre esta carga
     * académica (área × grado × sección × lapso).
     */
    public function eifinalks()
    {
        return $this->hasMany(\App\Models\app\Inicial\Eifinalk::class, 'pevaluacion_id');
    }

    public function escala()
    {
        return $this->belongsTo(Escala::class, 'escala_id');
    }

    // ─── STATIC METHODS ──────────────────────────────────────────

    /**
     * Cuenta las evaluaciones registradas por un profesor en un lapso.
     */
    public static function count_evaluacion_prof_lapso($profesor_id, $lapso_id)
    {
        $count = self::select(DB::raw('count(evaluacions.id) as value'))
            ->join('evaluacions', 'pevaluacions.id', '=', 'evaluacions.pevaluacion_id')
            ->where('pevaluacions.profesor_id', $profesor_id)
            ->where('pevaluacions.lapso_id', $lapso_id)
            ->whereNull('evaluacions.deleted_at')
            ->groupBy('pevaluacions.lapso_id')
            ->first();

        return ($count) ? (int) $count->value : 0;
    }

    // ─── SCOPES ──────────────────────────────────────────────────

    public function scopeActive($query, $flag = true)
    {
        return $query->where('pevaluacions.status_official', $flag);
    }

    public function scopeWithPlanningModule($query)
    {
        return $query->whereHas('pensum.pestudio', function ($q) {
            $q->where('planning_module', true);
        });
    }

    /**
     * Solo pevaluaciones cuyo pensum esté activo.
     *
     * `Pensum.status_active` está casteado a boolean, por eso se compara
     * contra `true` (a diferencia de Pestudio/Grado, que usan 'true').
     */
    public function scopeWithActivePensum($query)
    {
        return $query->whereHas('pensum', function ($q) {
            $q->where('pensums.status_active', true);
        });
    }

    /**
     * Solo pevaluaciones cuyo pensum NO esté activo (false o null).
     */
    public function scopeWithInactivePensum($query)
    {
        return $query->whereHas('pensum', function ($q) {
            $q->where(function ($q2) {
                $q2->where('pensums.status_active', false)
                    ->orWhereNull('pensums.status_active');
            });
        });
    }

    // ─── CARGA ACADÉMICA ACTIVA ─────────────────────────────────

    /**
     * Cargas académicas activas: cadena completa
     * pestudio → grado → sección → pensum → asignatura vigente.
     *
     * La cadena se resuelve de forma coherente:
     *  - `grados` se toma desde `seccions.grado_id` (el grado real de la sección).
     *  - `pensums` debe pertenecer a ese mismo grado y pestudio, de modo que una
     *    carga con pensum de otro plan no se considere activa.
     *
     * Todos los modelos de la cadena deben estar vigentes (sin `deleted_at`) y
     * con su bandera de estado activa. `Pestudio` no usa SoftDeletes y
     * `Pensum.status_active` está casteado a boolean.
     *
     * @param  int|null  $lapsoId  Limita al momento académico.
     * @param  int|null  $peducativoId  Limita al programa educativo.
     * @param  int|null  $pestudioId  Limita al plan de estudio.
     * @param  int|null  $gradoId  Limita al grado.
     * @param  int|null  $seccionId  Limita a la sección.
     * @param  int|null  $pensumId  Limita al área de formación.
     * @param  int|null  $asignaturaId  Limita a la asignatura.
     * @param  bool  $excludeInicial  Excluye profesores cuyo usuario tiene
     *                                `users.is_inicial = true` (usan otra
     *                                metodología para planificar actividades).
     */
    public function scopeActiveLoad(
        $query,
        ?int $lapsoId = null,
        ?int $peducativoId = null,
        ?int $pestudioId = null,
        ?int $gradoId = null,
        ?int $seccionId = null,
        ?int $pensumId = null,
        ?int $asignaturaId = null,
        bool $excludeInicial = false
    ) {
        return $query
            ->join('profesors', 'pevaluacions.profesor_id', '=', 'profesors.id')
            ->join('seccions', 'pevaluacions.seccion_id', '=', 'seccions.id')
            ->join('grados', 'seccions.grado_id', '=', 'grados.id')
            ->join('pestudios', 'grados.pestudio_id', '=', 'pestudios.id')
            ->join('pensums', 'pevaluacions.pensum_id', '=', 'pensums.id')
            ->join('asignaturas', 'pensums.asignatura_id', '=', 'asignaturas.id')
            // El pensum de la carga debe corresponder al grado y pestudio de la sección.
            ->whereColumn('pensums.grado_id', 'grados.id')
            ->whereColumn('pensums.pestudio_id', 'pestudios.id')
            ->whereNull('pevaluacions.deleted_at')
            ->whereNull('profesors.deleted_at')
            ->whereNull('seccions.deleted_at')
            ->whereNull('grados.deleted_at')
            ->whereNull('pestudios.deleted_at')
            ->whereNull('pensums.deleted_at')
            ->whereNull('asignaturas.deleted_at')
            ->where('profesors.status_active', 'true')
            ->where('seccions.status_active', 'true')
            ->where('grados.status_active', 'true')
            ->where('pestudios.status_active', 'true')
            ->where('pensums.status_active', true)
            ->when($lapsoId, fn ($q) => $q->where('pevaluacions.lapso_id', $lapsoId))
            ->when($peducativoId, fn ($q) => $q->where('pestudios.peducativo_id', $peducativoId))
            ->when($pestudioId, fn ($q) => $q->where('pestudios.id', $pestudioId))
            ->when($gradoId, fn ($q) => $q->where('grados.id', $gradoId))
            ->when($seccionId, fn ($q) => $q->where('seccions.id', $seccionId))
            ->when($pensumId, fn ($q) => $q->where('pensums.id', $pensumId))
            ->when($asignaturaId, fn ($q) => $q->where('asignaturas.id', $asignaturaId))
            ->when($excludeInicial, fn ($q) => $q->whereNotExists(function ($sub) {
                $sub->select(DB::raw(1))
                    ->from('users')
                    ->whereColumn('users.id', 'profesors.user_id')
                    ->where('users.is_inicial', true);
            }));
    }

    /**
     * Builder de profesores con carga académica activa, listo para consultar
     * (con `distinct` y orden alfabético por apellido/nombre).
     */
    public static function queryProfesoresConCargaActiva(
        ?int $lapsoId = null,
        ?int $peducativoId = null,
        ?int $pestudioId = null,
        ?int $gradoId = null,
        ?int $seccionId = null,
        ?int $pensumId = null,
        ?int $asignaturaId = null,
        bool $excludeInicial = false
    ): Builder {
        $profesorIds = static::query()
            ->activeLoad($lapsoId, $peducativoId, $pestudioId, $gradoId, $seccionId, $pensumId, $asignaturaId, $excludeInicial)
            ->select('pevaluacions.profesor_id')
            ->toBase();

        return Profesor::query()
            ->select('profesors.*')
            ->whereIn('profesors.id', $profesorIds)
            ->distinct()
            ->orderBy('profesors.lastname')
            ->orderBy('profesors.name');
    }

    /**
     * Profesores con carga académica activa para los filtros indicados.
     */
    public static function getProfesoresConCargaActiva(
        ?int $lapsoId = null,
        ?int $peducativoId = null,
        ?int $pestudioId = null,
        ?int $gradoId = null,
        ?int $seccionId = null,
        ?int $pensumId = null,
        ?int $asignaturaId = null,
        bool $excludeInicial = false
    ): Collection {
        return static::queryProfesoresConCargaActiva(
            $lapsoId,
            $peducativoId,
            $pestudioId,
            $gradoId,
            $seccionId,
            $pensumId,
            $asignaturaId,
            $excludeInicial
        )->get();
    }

    // ─── ACCESSORS ───────────────────────────────────────────────

    public function getFullNameAttribute()
    {
        $asignatura = $this->pensum?->asignatura?->name ?? '?';
        $seccion = $this->seccion?->name ?? '?';
        $lapso = $this->lapso?->name ?? '?';

        return "{$asignatura} - {$seccion} ({$lapso})";
    }

    public function getIsLapsoClosedAttribute()
    {
        return $this->lapso && $this->lapso->ffinal
            ? now()->gt($this->lapso->ffinal)
            : false;
    }

    // ─── COMPETENCIAS ────────────────────────────────────────────

    /**
     * Obtiene las competencias del pensum asociado con sus indicadores
     * y referentes normativos, como una colección.
     */
    public function getCompetenciasWithIndicators(): ?\Illuminate\Support\Collection
    {
        return $this->pensum?->diagCompetencies()
            ->with(['indicators', 'referent'])
            ->get();
    }
}
