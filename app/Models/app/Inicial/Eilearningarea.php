<?php

namespace App\Models\app\Inicial;

use App\Models\app\Academy\Grado;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Área de aprendizaje del currículo de Educación Inicial.
 *
 * El mismo catálogo (las 9 áreas) se repite para cada grupo de edad:
 * grado 22 = 1ER GRUPO, 23 = 2DO GRUPO, 24 = 3ER GRUPO.
 * Lo siembra `Database\Seeders\EILearningSeeder`.
 *
 * ⚠️ `description` es `text NOT NULL` sin default en el DDL: toda inserción
 * debe indicar `description` de forma explícita (el seeder del legacy no lo
 * hacía y habría fallado bajo `sql_mode` estricto).
 *
 * @property int $grado_id
 * @property string $name
 * @property string $description
 */
class Eilearningarea extends Model
{
    use HasFactory;

    protected $table = 'eilearningareas';

    protected $fillable = [
        'grado_id',
        'name',
        'description',
    ];

    protected $casts = [
        'grado_id' => 'integer',
    ];

    const COLUMN_COMMENTS = [
        'grado_id' => 'Grupo de edad: Grupo 1, 2, 3',
        'name' => 'Nombre del área de aprendizaje',
        'description' => 'Descripción del área aprendizaje',
    ];

    // ─── RELACIONES ──────────────────────────────────────────────

    public function grado()
    {
        return $this->belongsTo(Grado::class, 'grado_id');
    }

    public function expectations()
    {
        return $this->hasMany(Eilearningexpectation::class, 'eilearningarea_id');
    }

    // ─── SCOPES ──────────────────────────────────────────────────

    public function scopeByGrado($query, $gradoId)
    {
        return $query->where('grado_id', $gradoId);
    }

    /**
     * Búsqueda por nombre o descripción.
     *
     * ⚠️ El legacy no agrupaba las condiciones con `where(...)`, así que este
     * scope se combinaba mal con cualquier `where` previo (un `orWhere`
     * suelto se evalúa a nivel de tabla y anula los filtros del scope
     * llamador). Corregido aquí agrupando en una closure.
     */
    public function scopeSearch($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('name', 'like', "%{$search}%")
                ->orWhere('description', 'like', "%{$search}%");
        });
    }

    /**
     * "Activa" = área que tiene al menos una expectativa de aprendizaje.
     *
     * ⚠️ Consecuencia consciente (blueprint/inicial §6.14): un área sin
     * expectativas desaparece de este scope. Es el comportamiento del legacy y
     * se mantiene a propósito, pero hay que tenerlo presente al usarlo para
     * poblar selects: tras el seeding las 27 áreas tienen 5 expectativas cada
     * una, así que el scope no oculta nada.
     */
    public function scopeActive($query)
    {
        return $query->whereHas('expectations');
    }

    // ─── ACCESORS ────────────────────────────────────────────────

    /**
     * Nombre completo del área incluyendo el grupo de edad.
     */
    public function getNombreCompletoAttribute()
    {
        return "{$this->name} - Grupo {$this->grado?->name}";
    }

    public function getExpectationsCountAttribute()
    {
        return $this->expectations()->count();
    }

    // ─── MÉTODOS ─────────────────────────────────────────────────

    public function hasExpectations()
    {
        return $this->expectations()->exists();
    }

    /**
     * Expectativas del área que tienen descripción cargada.
     */
    public function getActiveExpectations()
    {
        return $this->expectations()
            ->whereNotNull('description')
            ->get();
    }

    /**
     * Informes finales que han vinculado alguna expectativa de esta área.
     *
     * La columna va CALIFICADA a propósito: el `whereHas` sobre `expectations`
     * hace join con `eifinalk_expectation`, que también tiene
     * `eilearningarea_id`. Sin calificar, MySQL responde
     * `1052 Column 'eilearningarea_id' in WHERE clause is ambiguous` — que es
     * exactamente lo que pasaba en el legacy.
     */
    public function getRelatedEifinalks()
    {
        return Eifinalk::whereHas('expectations', function ($query) {
            $query->where('eilearningexpectations.eilearningarea_id', $this->id);
        })->get();
    }
}
