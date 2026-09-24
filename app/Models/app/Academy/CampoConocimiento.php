<?php

namespace App\Models\app\Academy;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CampoConocimiento extends Model
{
    use HasFactory;

    protected $fillable = [
        'area_conocimiento_id', 'asignatura_id', 'pensum_id', 'observations', 'order',
    ];

    protected $table = 'campo_conocimientos';

    public function area_conocimiento()
    {
        return $this->belongsTo(AreaConocimiento::class, 'area_conocimiento_id');
    }

    public function asignatura()
    {
        return $this->belongsTo(Asignatura::class, 'asignatura_id');
    }

    public function pensum()
    {
        return $this->belongsTo(Pensum::class, 'pensum_id');
    }

    /**
     * Resuelve el pensum_id que corresponde a una asignatura dentro de un área
     * de conocimiento, siguiendo la cadena pevaluacion → pensum → asignatura.
     *
     * Prioridad:
     *  1) pensum cuyo pestudio coincide con el del área (si hay exactamente 1),
     *  2) si la asignatura tiene un único pensum,
     *  3) el pensum con más pevaluaciones (el realmente impartido).
     *
     * Devuelve null si no es posible resolverlo de forma inequívoca.
     */
    public static function resolvePensumId(int $asignaturaId, ?int $areaPestudioId): ?int
    {
        $pensums = Pensum::where('asignatura_id', $asignaturaId)
            ->where('status_active', true)
            ->whereHas('grado', fn ($q) => $q->where('status_active', 'true'))
            ->get();

        if ($pensums->isEmpty()) {
            return null;
        }

        if ($areaPestudioId) {
            $byPestudio = $pensums->where('pestudio_id', $areaPestudioId);
            if ($byPestudio->count() === 1) {
                return (int) $byPestudio->first()->id;
            }
        }

        if ($pensums->count() === 1) {
            return (int) $pensums->first()->id;
        }

        $best = $pensums->sortByDesc(function (Pensum $pensum) {
            return $pensum->pevaluacions()->count();
        })->first();

        return $best ? (int) $best->id : null;
    }
}
