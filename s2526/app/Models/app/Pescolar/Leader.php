<?php

namespace App\Models\app\Pescolar;

use App\Models\app\Profesor\Pevaluacion;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Leader extends Model
{
    use HasFactory;

    public static function getGradosForLeader($leader_id)
    {
        return Grado::whereHas('pestudio.pensums', function ($query) use ($leader_id) {
            $query->whereHas('asignatura.campoConocimientos.areaConocimiento', function ($query) use ($leader_id) {
                $query->where('leader_id', $leader_id);
            })->whereNull('deleted_at');
        })->get();
    }


    public static function getPeducativosForLeader($leader_id)
    {
        return Peducativo::whereHas('pestudios', function ($query) use ($leader_id) {
            $query->whereHas('grados.pensums', function ($query) use ($leader_id) {
                $query->whereHas('asignatura.campoConocimientos.areaConocimiento', function ($query) use ($leader_id) {
                    $query->where('leader_id', $leader_id);
                })->whereNull('deleted_at');
            });
        })->get();
    }

    public static function getPestudioForLeader($leader_id)
    {
        return Pestudio::whereHas('grados', function ($query) use ($leader_id) {
            $query->whereHas('pensums', function ($query) use ($leader_id) {
                $query->whereHas('asignatura.campoConocimientos.areaConocimiento', function ($query) use ($leader_id) {
                    $query->where('leader_id', $leader_id);
                });
            });
        })->get();
    }

    public static function getPensumsForLeader($leaderId, $gradoId = null)
    {
        return Pensum::select('pensums.*','pensums.id as pensum_id') // Seleccionar el ID del pensum
            ->selectRaw("CONCAT('[', asignaturas.code, '] ', asignaturas.name) as asignatura_fullname") // Columna concatenada
            ->join('asignaturas', 'pensums.asignatura_id', '=', 'asignaturas.id') // Unir con asignaturas
            ->orderBy('asignaturas.name')
            ->whereHas('asignatura.campoConocimientos.areaConocimiento', function ($query) use ($leaderId) {
                $query->where('leader_id', $leaderId); // Filtrar por líder
            })
            ->when($gradoId, function ($query) use ($gradoId) {
                $query->whereHas('grado', function ($query) use ($gradoId) {
                    $query->where('id', $gradoId); // Filtrar por grado si $gradoId no es nulo
                });
            })
            ->whereNull('pensums.deleted_at') // Filtrar solo pensums activos
            ->get();
    }

    public static function getPevaluacionesForLeaderSimple($leaderId)
    {
        return Pevaluacion::with([
                'pensum.asignatura.campoConocimientos.areaConocimiento',
            ])
            ->whereHas('pensum.asignatura.campoConocimientos.areaConocimiento', function ($query) use ($leaderId) {
                $query->where('leader_id', $leaderId); // Filtrar por líder
            })
            ->whereNull('pevaluacions.deleted_at') // Filtrar evaluaciones activas
            ->orderBy('created_at', 'desc') // Ordenar por fecha de creación, ajustable según necesidad
            ->get();
    }

    public static function getPevaluacionesForLeader($leaderId, $filters = [], $paginate = false, $perPage = 15)
    {
        $pevaluacions =  Pevaluacion::with([
                'pensum.asignatura.campoConocimientos.areaConocimiento',
                'seccion.grado',
                'profesor',
                'lapso',
                'pensum',
            ])
            ->withCount('activities') // Contar las actividades relacionadas
            ->whereHas('pensum.pestudio', function ($query) {
                $query->where('planning_module', true); // Solo pestudios con módulo de planificación activo
            })
            //->whereHas('pensum.asignatura.campoConocimientos.areaConocimiento', function ($query) use ($leaderId) {
                //$query->where('leader_id', $leaderId); // Filtrar por líder
            //})
            ->when(isset($filters['seccion_id']), function ($query) use ($filters) {
                $query->where('seccion_id', $filters['seccion_id']);
            })
            ->when(isset($filters['grado_id']), function ($query) use ($filters) {
                $query->whereHas('seccion.grado', function ($query) use ($filters) {
                    $query->where('id', $filters['grado_id']);
                });
            })
            ->when(isset($filters['lapso_id']), function ($query) use ($filters) {
                $query->where('lapso_id', $filters['lapso_id']);
            })
            ->when(isset($filters['pestudio_id']), function ($query) use ($filters) {
                $query->whereHas('pensum.pestudio', function ($query) use ($filters) {
                    $query->where('id', $filters['pestudio_id']);
                });
            })
            ->when(isset($filters['pensum_id']), function ($query) use ($filters) {
                $query->where('pensum_id', $filters['pensum_id']);
            })
            ->when(isset($filters['profesor_id']), function ($query) use ($filters) {
                $query->where('profesor_id', $filters['profesor_id']);
            })

            ->when(isset($filters['status_activities']), function ($query) use ($filters) {
                if ($filters['status_activities'] == 'SI') {
                    $query->having('activities_count', '>', 0); // Filtrar con actividades
                } elseif ($filters['status_activities'] =="NO") {
                    $query->having('activities_count', '=', 0); // Filtrar sin actividades
                }
            })

            ->whereNull('pevaluacions.deleted_at') // Filtrar evaluaciones activas
            ->orderBy('created_at', 'desc') // Ordenar por fecha de creación
            ;

        if ($paginate) {
            if ((int) $perPage === 9999) {
                $all = $pevaluacions->get();
                // Retorna un LengthAwarePaginator con todos los resultados en 1 página
                // para que la vista sea compatible con ->links() y ->onEachSide()
                return new \Illuminate\Pagination\LengthAwarePaginator(
                    $all,
                    $all->count(),
                    max($all->count(), 1),
                    1,
                    ['path' => \Illuminate\Pagination\Paginator::resolveCurrentPath()]
                );
            }
            return $pevaluacions->paginate($perPage);
        }

        return $pevaluacions->get();
    }

    public static function getEvaluacionesForLeader2($leaderId, array $filters = [], $paginate = false, $perPage = 15)
    {
        $query = Pevaluacion::with([
            'pensum',
            'seccion.grado',
            'profesor',
            'lapso',
        ])
        ->whereHas('pensum.asignatura.campoConocimientos.areaConocimiento', function ($query) use ($leaderId) {
            $query->where('leader_id', $leaderId);
        });

        // Aplicar filtros dinámicamente
        foreach ($filters as $key => $value) {
            $query->where($key, $value);
        }

        // Verificar si usar paginación
        return $paginate ? $query->paginate($perPage) : $query->get();
    }


}
