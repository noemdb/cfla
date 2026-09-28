<?php

namespace App\Console\Commands;

use App\Models\app\Academy\Asignatura;
use App\Models\app\Academy\Grado;
use App\Models\app\Academy\Peducativo;
use App\Models\app\Academy\Pensum;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

/**
 * Amplía el pensum de cada grado activo de PRIMARIA y MEDIA GENERAL con un
 * área complementaria de apoyo pedagógico.
 *
 * Paso 1 · Registra una Asignatura por grado:
 *   - Primaria:      "ÁREA COMPLEMENTARIA APOYO PEDAGÓGICO {code_sm}" (p. ej. 1G)
 *   - Media General: "APOYO PEDAGÓGICO {ord}.A" (p. ej. 1ER.A)
 *
 * Paso 2 · Registra un Pensum por cada asignatura creada, con el grado_id
 * que corresponda (pestudio_id heredado del grado).
 *
 * Es idempotente: si la asignatura (por pestudio_id + code) o el pensum
 * (por grado_id + asignatura_id) ya existen —incluso soft-deleted, en cuyo
 * caso se restauran—, se reutilizan en vez de duplicarse.
 *
 * Uso:
 *   php8.2 artisan pensum:ampliar-apoyo-pedagogico --dry-run
 *   php8.2 artisan pensum:ampliar-apoyo-pedagogico
 */
class AmpliarPensumApoyoPedagogico extends Command
{
    protected $signature = 'pensum:ampliar-apoyo-pedagogico
        {--dry-run : Solo auditoría, no persiste cambios}';

    protected $description = 'Registra asignaturas y pensums de apoyo pedagógico en los grados activos de Primaria y Media General';

    private const ORDINALES = [
        1 => '1ER',
        2 => '2DO',
        3 => '3ER',
        4 => '4TO',
        5 => '5TO',
        6 => '6TO',
    ];

    public function handle(): int
    {
        $dryRun = (bool) $this->option('dry-run');

        $peducativos = Peducativo::where('peducativos.status_active', 'true')
            ->where(function ($q) {
                $q->where('peducativos.name', 'like', '%PRIMARIA%')
                    ->orWhere('peducativos.name', 'like', '%MEDIA%GENERAL%');
            })
            ->pluck('id', 'name');

        if ($peducativos->isEmpty()) {
            $this->error('No se encontraron peducativos activos de PRIMARIA / MEDIA GENERAL.');

            return self::FAILURE;
        }

        $grados = Grado::select('grados.*')
            ->join('pestudios', 'pestudios.id', '=', 'grados.pestudio_id')
            ->join('peducativos', 'peducativos.id', '=', 'pestudios.peducativo_id')
            ->whereIn('peducativos.id', $peducativos->values())
            ->where('peducativos.status_active', 'true')
            ->where('pestudios.status_active', 'true')
            ->where('grados.status_active', 'true')
            ->whereNull('peducativos.deleted_at')
            ->whereNull('pestudios.deleted_at')
            ->whereNull('grados.deleted_at')
            ->with('pestudio.peducativo')
            ->orderBy('peducativos.id')
            ->orderBy('pestudios.id')
            ->orderBy('grados.order')
            ->get();

        if ($grados->isEmpty()) {
            $this->error('No hay grados activos en PRIMARIA / MEDIA GENERAL.');

            return self::FAILURE;
        }

        $this->info(sprintf(
            'Grados activos a procesar: %d%s',
            $grados->count(),
            $dryRun ? ' (dry-run, sin persistir)' : ''
        ));

        $rows = [];
        $createdAsignaturas = 0;
        $createdPensums = 0;

        $persist = function () use ($grados, &$rows, &$createdAsignaturas, &$createdPensums, $dryRun) {
            foreach ($grados as $grado) {
                $isPrimaria = str_contains(mb_strtoupper($grado->pestudio->peducativo->name ?? ''), 'PRIMARIA');
                $spec = $isPrimaria
                    ? $this->specPrimaria($grado)
                    : $this->specMedia($grado);

                $asignatura = Asignatura::withTrashed()
                    ->where('pestudio_id', $grado->pestudio_id)
                    ->where('code', $spec['code'])
                    ->first();

                $asignaturaEstado = 'existente';
                if ($asignatura === null) {
                    $asignaturaEstado = $dryRun ? 'nueva' : 'creada';
                    if (! $dryRun) {
                        $asignatura = Asignatura::create($spec['attributes']);
                        $createdAsignaturas++;
                    } else {
                        $asignatura = new Asignatura($spec['attributes']);
                    }
                } elseif ($asignatura->trashed()) {
                    $asignaturaEstado = $dryRun ? 'restauraría' : 'restaurada';
                    if (! $dryRun) {
                        $asignatura->restore();
                    }
                }

                $asignaturaId = $asignatura->id; // null en dry-run si es nueva
                $pensumEstado = 'existente';
                $pensum = $asignaturaId
                    ? Pensum::withTrashed()
                        ->where('grado_id', $grado->id)
                        ->where('asignatura_id', $asignaturaId)
                        ->first()
                    : null;

                if ($pensum === null) {
                    $pensumEstado = $dryRun ? 'nuevo' : 'creado';
                    if (! $dryRun) {
                        Pensum::create([
                            'pestudio_id' => $grado->pestudio_id,
                            'grado_id' => $grado->id,
                            'asignatura_id' => $asignaturaId,
                            'status_component' => 'false',
                            'status_active' => true,
                            'status_active_diagnostic' => false,
                            'observations' => $spec['pensum_observations'],
                        ]);
                        $createdPensums++;
                    }
                } elseif ($pensum->trashed()) {
                    $pensumEstado = $dryRun ? 'restauraría' : 'restaurado';
                    if (! $dryRun) {
                        $pensum->restore();
                    }
                }

                $rows[] = [
                    $grado->pestudio->code ?? $grado->pestudio_id,
                    "[{$grado->code_sm}] {$grado->name}",
                    $spec['code'],
                    $spec['attributes']['name'],
                    $asignaturaEstado,
                    $pensumEstado,
                ];
            }
        };

        if ($dryRun) {
            $persist();
        } else {
            DB::transaction($persist);
        }

        $this->table(
            ['Pestudio', 'Grado', 'Código', 'Asignatura', 'Asignatura', 'Pensum'],
            $rows
        );

        if ($dryRun) {
            $this->info('--dry-run: sin cambios persistidos. Re-ejecuta sin el flag para aplicar.');
        } else {
            $this->info("Listo: {$createdAsignaturas} asignaturas y {$createdPensums} pensums creados (el resto ya existía).");
        }

        return self::SUCCESS;
    }

    /**
     * @return array{code: string, attributes: array, pensum_observations: string}
     */
    private function specPrimaria(Grado $grado): array
    {
        $name = "ÁREA COMPLEMENTARIA APOYO PEDAGÓGICO {$grado->code_sm}";

        return [
            'code' => "COM-AP-{$grado->code_sm}",
            'attributes' => [
                'pestudio_id' => $grado->pestudio_id,
                'code' => "COM-AP-{$grado->code_sm}",
                'code_sm' => "AP{$grado->order}",
                'name' => $name,
                'tescala' => 'NUMÉRICA',
                'order' => 12,
                'hour_t_week' => 1,
                'hour_p_week' => 1,
                'enable_academic_index' => 'false',
                'enable_lost_regulation' => 'false',
                'enable_official_doc' => 'true',
                'enable_repairable' => 'false',
                'enable_grupo_estable' => 'false',
                'observations' => $name,
            ],
            'pensum_observations' => 'ÁREA COMPLEMENTARIA APOYO PEDAGÓGICO',
        ];
    }

    /**
     * @return array{code: string, attributes: array, pensum_observations: string}
     */
    private function specMedia(Grado $grado): array
    {
        $ordinal = self::ORDINALES[(int) $grado->order] ?? "{$grado->order}TO";
        $name = "APOYO PEDAGÓGICO {$ordinal}.A";

        return [
            'code' => "APO-PED-{$grado->code_sm}",
            'attributes' => [
                'pestudio_id' => $grado->pestudio_id,
                'code' => "APO-PED-{$grado->code_sm}",
                'code_sm' => 'AP',
                'name' => $name,
                'tescala' => 'NUMÉRICA',
                'order' => 12,
                'hour_t_week' => 1,
                'hour_p_week' => 1,
                'enable_academic_index' => 'false',
                'enable_lost_regulation' => 'false',
                'enable_official_doc' => 'true',
                'enable_repairable' => 'false',
                'enable_grupo_estable' => 'false',
                'observations' => $name,
            ],
            'pensum_observations' => 'APOYO PEDAGÓGICO',
        ];
    }
}
