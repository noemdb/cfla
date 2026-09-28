<?php

namespace App\Services\Planning;

use App\Models\app\Academy\Grado;
use App\Models\app\Academy\Inscripcion;
use App\Models\app\Academy\Seccion;
use App\Models\app\Learner\Estudiant;
use App\Models\app\Learner\Representant;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * Importador de inscripciones desde CSV.
 *
 * Responsabilidades:
 *  - Parsear el archivo (delimitador autodetectado, BOM, Windows-1252).
 *  - Construir una vista previa con la acción por fila (crear/inscribir/actualizar).
 *  - Ejecutar la importación con reglas de negocio (crear estudiante, actualizar
 *    sección, datos académicos y nombres según opciones).
 *
 * Opciones soportadas (array asociativo):
 *  - pestudio_id          ?int    Filtra/desambigua el grado.
 *  - tipo_id              int     Tipo de inscripción (obligatorio al crear).
 *  - escolaridad_id       int     Escolaridad (obligatorio al crear).
 *  - programacion_id      int     Programación (obligatorio al crear).
 *  - grupo_estable_id     ?int    Grupo estable (opcional).
 *  - plan_pago_id         ?int    Plan de pago para estudiantes nuevos.
 *  - representant_ci      ?string CI del representante para estudiantes nuevos.
 *  - update_academic_data bool    Actualiza tipo/escolaridad/programación/grupo
 *                                 en inscripciones existentes.
 *  - update_student_names bool    Actualiza nombre/apellido del estudiante.
 *  - observations_note    ?string Nota de origen a guardar en observations.
 *
 * Detección de duplicados (paso 2, vista previa):
 *  - Primer criterio, siempre: la CI normalizada (solo dígitos).
 *  - Segundo criterio: el nombre [lastname, name] normalizado (insensible a
 *    mayúsculas, tildes y espacios). Si la CI no existe pero el nombre sí
 *    (con otra CI), la fila se señala con `requires_review = true`:
 *      · con inscripción en la misma sección → acción `actualizar_ci`
 *        (solo se actualiza la CI);
 *      · con inscripción en otra sección → acción `actualizar`
 *        (se actualiza la CI y la sección);
 *      · sin inscripción → acción `crear` (estudiante nuevo, con aviso).
 *  - Cada fila lleva `selected` (checkbox aceptar/descartar de la vista
 *    previa, marcado por defecto). Las filas con `selected = false` se
 *    omiten en `import()` y cuentan como `discarded`.
 */
class InscripcionCsvImporter
{
    /** @var Collection<int, Grado>|null */
    private ?Collection $grados = null;

    /** @var array<int, Collection<int, Seccion>> */
    private array $seccionesPorGrado = [];

    private ?int $defaultPlanPagoId = null;

    private ?int $sentinelRepresentantId = null;

    /** @var array<string, array<int, Estudiant>>|null Índice nombre normalizado → estudiantes. */
    private ?array $nameIndex = null;

    // ─── Parseo ────────────────────────────────────────────────

    /**
     * @return array<int, array<string, mixed>>
     */
    public function parse(string $path): array
    {
        $content = file_get_contents($path);
        if ($content === false) {
            throw new \RuntimeException('No se pudo abrir el archivo.');
        }

        $content = $this->normalizeFileEncoding($content);

        $firstLine = strtok($content, "\r\n");
        $delimiter = $this->detectDelimiter($firstLine === false ? false : $firstLine);

        $handle = fopen('php://memory', 'r+');
        fwrite($handle, $content);
        rewind($handle);

        $rows = [];
        $header = null;
        $line = 0;

        while (($data = fgetcsv($handle, 0, $delimiter)) !== false) {
            $line++;

            if (count($data) === 1 && trim((string) $data[0]) === '') {
                continue;
            }

            $data = array_map(fn ($value) => $this->toUtf8(trim((string) $value)), $data);

            if ($header === null) {
                $detected = $this->mapHeader($data);
                if ($detected !== null) {
                    $header = $detected;

                    continue;
                }

                $header = ['ci_estudiant', 'lastname', 'name', 'grado', 'seccion'];
            }

            $row = ['_line' => $line];
            foreach ($header as $index => $field) {
                $row[$field] = $data[$index] ?? null;
            }

            $rows[] = $row;
        }

        fclose($handle);

        return $rows;
    }

    /**
     * Normaliza el contenido del archivo a UTF-8. Soporta:
     *  - UTF-16 LE/BE con BOM (export "Unicode Text" de Excel).
     *  - UTF-8 con BOM.
     *  - Windows-1252 / ISO-8859-1.
     */
    public function normalizeFileEncoding(string $content): string
    {
        if (str_starts_with($content, "\xFF\xFE")) {
            return mb_convert_encoding(substr($content, 2), 'UTF-8', 'UTF-16LE');
        }

        if (str_starts_with($content, "\xFE\xFF")) {
            return mb_convert_encoding(substr($content, 2), 'UTF-8', 'UTF-16BE');
        }

        if (str_starts_with($content, "\xEF\xBB\xBF")) {
            return substr($content, 3);
        }

        if (! mb_check_encoding($content, 'UTF-8')) {
            return mb_convert_encoding($content, 'UTF-8', 'Windows-1252');
        }

        return $content;
    }

    protected function detectDelimiter(string|false $firstLine): string
    {
        if ($firstLine === false) {
            return ',';
        }

        $counts = [
            ',' => substr_count($firstLine, ','),
            ';' => substr_count($firstLine, ';'),
            "\t" => substr_count($firstLine, "\t"),
        ];
        arsort($counts);

        return array_key_first($counts) ?: ',';
    }

    /**
     * @param  array<int, string>  $cells
     * @return array<int, string>|null
     */
    public function mapHeader(array $cells): ?array
    {
        $map = [];

        foreach ($cells as $index => $cell) {
            $key = $this->normalizeHeader($cell);
            if ($key === '') {
                continue;
            }

            if (str_contains($key, 'apellido') || str_contains($key, 'lastname')) {
                $map[$index] = 'lastname';
            } elseif (str_contains($key, 'nombre') || str_contains($key, 'name')) {
                $map[$index] = 'name';
            } elseif (str_starts_with($key, 'ci') || str_contains($key, 'cedula')) {
                $map[$index] = 'ci_estudiant';
            } elseif (str_contains($key, 'grado') || str_contains($key, 'nivel')) {
                $map[$index] = 'grado';
            } elseif (str_contains($key, 'seccion')) {
                $map[$index] = 'seccion';
            }
        }

        if (! in_array('ci_estudiant', $map, true)
            || ! in_array('grado', $map, true)
            || ! in_array('seccion', $map, true)) {
            return null;
        }

        return $map;
    }

    // ─── Vista previa ──────────────────────────────────────────

    /**
     * @param  array<int, array<string, mixed>>  $rows
     * @param  array<string, mixed>  $options
     * @return array<int, array<string, mixed>>
     */
    public function preview(array $rows, array $options = []): array
    {
        $pestudioId = $this->intOrNull($options['pestudio_id'] ?? null);
        $updateAcademic = (bool) ($options['update_academic_data'] ?? false);
        $updateNames = (bool) ($options['update_student_names'] ?? false);

        $preview = [];
        $seen = [];
        $seenNames = [];
        $this->nameIndex = null;

        foreach ($rows as $row) {
            $ciOriginal = trim((string) ($row['ci_estudiant'] ?? ''));
            // Los CI marcados como generados (filas sin cédula) conservan su
            // formato alfanumérico; el resto se normaliza a dígitos.
            $ciGenerated = ! empty($row['ci_generated']);
            $ci = $ciGenerated
                ? $this->normalizeGeneratedCi($ciOriginal)
                : $this->normalizeCi($ciOriginal);
            $lastname = trim((string) ($row['lastname'] ?? ''));
            $name = trim((string) ($row['name'] ?? ''));
            $gradoName = trim((string) ($row['grado'] ?? ''));
            $seccionName = trim((string) ($row['seccion'] ?? ''));

            $item = [
                'line' => $row['_line'] ?? null,
                'ci' => $ci,
                'ci_original' => $ciOriginal,
                'ci_generated' => $ciGenerated,
                'lastname' => $lastname,
                'name' => $name,
                'grado' => $gradoName,
                'seccion' => $seccionName,
                'seccion_label' => null,
                'status' => 'ok',
                'action' => '',
                'message' => '',
                'duplicate' => false,
                'estudiant_id' => null,
                'seccion_id' => null,
                // Paso 2: selección manual y segunda coincidencia por nombre.
                'selected' => true,
                'requires_review' => false,
                'name_match' => false,
                'name_match_id' => null,
                'name_match_ci' => null,
            ];

            if ($ci === '' || $gradoName === '' || $seccionName === '') {
                $preview[] = $this->fail($item, 'Faltan datos (CI, grado o sección).');

                continue;
            }

            if (mb_strlen($ci) > 191 || mb_strlen($lastname) > 191 || mb_strlen($name) > 191) {
                $preview[] = $this->fail($item, 'Alguno de los campos excede la longitud permitida (191 caracteres).');

                continue;
            }

            $seccion = $this->resolveSeccion($gradoName, $seccionName, $pestudioId);
            if (! $seccion) {
                $suffix = $pestudioId
                    ? ' en el plan de estudio seleccionado.'
                    : '. Selecciona un Plan de Estudio si el grado está repetido.';
                $preview[] = $this->fail(
                    $item,
                    "No se encontró la sección \"{$seccionName}\" para el grado \"{$gradoName}\"{$suffix}"
                );

                continue;
            }

            $item['seccion_id'] = $seccion->id;
            $item['seccion_label'] = ($seccion->grado?->name ?? $gradoName).' · '.$seccion->name;

            // Duplicado dentro del mismo archivo.
            if (isset($seen[$ci])) {
                $previous = $seen[$ci];
                $item['duplicate'] = true;
                $item['estudiant_id'] = $previous['estudiant_id'];
                $item['action'] = ((int) $previous['seccion_id'] === (int) $seccion->id) ? 'sin_cambios' : 'actualizar';
                $item['message'] = "CI duplicado en el archivo (línea {$previous['line']}). ".($item['action'] === 'sin_cambios'
                    ? 'Misma sección; sin cambios.'
                    : 'Se actualizará la sección.');
                $preview[] = $item;

                continue;
            }

            $estudiant = $this->findEstudiantByCi($ci);
            if ($estudiant) {
                $item['estudiant_id'] = $estudiant->id;
                $existing = Inscripcion::where('estudiant_id', $estudiant->id)->first();

                if ($existing) {
                    $changes = [];
                    if ((int) $existing->seccion_id !== (int) $seccion->id) {
                        $changes[] = 'sección';
                    }
                    if ($updateAcademic && $this->academicChanges($existing, $options)) {
                        $changes[] = 'datos académicos';
                    }
                    if ($updateNames && $this->nameChanges($estudiant, $lastname, $name)) {
                        $changes[] = 'nombre/apellido';
                    }

                    if ($changes) {
                        $item['action'] = 'actualizar';
                        $item['message'] = 'Ya inscrito; se actualizará: '.implode(', ', $changes).'.';
                    } else {
                        $item['action'] = 'sin_cambios';
                        $item['message'] = 'Ya inscrito; sin cambios.';
                    }
                } else {
                    $item['action'] = 'inscribir';
                    $item['message'] = 'Estudiante existente; se inscribirá.';
                }
            } else {
                $item['action'] = 'crear';
                $item['message'] = 'Estudiante nuevo; se creará y se inscribirá.';

                // Segundo criterio (el primero siempre es la CI): si el nombre
                // [lastname, name] ya existe en BD con otra CI, se señala.
                $nameMatches = array_values(array_filter(
                    $this->findEstudiantsByName($lastname, $name),
                    fn ($candidate) => $this->normalizeCi((string) $candidate->ci_estudiant) !== $ci
                ));

                if ($nameMatches !== []) {
                    $match = $nameMatches[0];
                    $item['name_match'] = true;
                    $item['name_match_id'] = (int) $match->id;
                    $item['name_match_ci'] = (string) $match->ci_estudiant;
                    $item['requires_review'] = true;

                    $existingByName = Inscripcion::where('estudiant_id', $match->id)->first();

                    if ($existingByName && (int) $existingByName->seccion_id === (int) $seccion->id) {
                        // Mismo estudiante con CI actualizada: solo se actualiza la CI.
                        $item['estudiant_id'] = (int) $match->id;
                        $item['action'] = 'actualizar_ci';
                        $item['message'] = "El nombre coincide con {$match->lastname} {$match->name} (CI {$match->ci_estudiant}) ya inscrito en esta sección; se actualizará solo la CI a {$ci}.";
                    } elseif ($existingByName) {
                        // Inscrito en otra sección: se actualiza la CI y la sección.
                        $item['estudiant_id'] = (int) $match->id;
                        $item['action'] = 'actualizar';
                        $item['message'] = "El nombre coincide con {$match->lastname} {$match->name} (CI {$match->ci_estudiant}) inscrito en otra sección; se actualizará la CI a {$ci} y la sección.";
                    } else {
                        // Sin inscripción: se trata como estudiante nuevo, con aviso.
                        $item['action'] = 'crear';
                        $item['message'] = "Existe {$match->lastname} {$match->name} con CI {$match->ci_estudiant} sin inscripción; se creará como estudiante nuevo con CI {$ci}. Desmárcalo para omitirlo.";
                    }

                    if (count($nameMatches) > 1) {
                        $item['message'] .= ' Hay '.count($nameMatches).' registros con ese nombre; se usa el primero.';
                    }
                }
            }

            // Aviso intra-archivo: mismo nombre con CI distinta en otra fila.
            $nameKey = $this->normalizeFullName($lastname, $name);
            if ($nameKey !== '' && isset($seenNames[$nameKey]) && $seenNames[$nameKey]['ci'] !== $ci) {
                $item['requires_review'] = true;
                $item['message'] .= ($item['message'] !== '' ? ' ' : '')
                    ."Nombre repetido en el archivo con CI distinta (línea {$seenNames[$nameKey]['line']}).";
            }

            $seen[$ci] = [
                'line' => $item['line'],
                'seccion_id' => $seccion->id,
                'estudiant_id' => $item['estudiant_id'],
            ];

            if ($nameKey !== '') {
                $seenNames[$nameKey] = ['line' => $item['line'], 'ci' => $ci];
            }

            $preview[] = $item;
        }

        return $preview;
    }

    // ─── Importación ───────────────────────────────────────────

    /**
     * @param  array<int, array<string, mixed>>  $preview
     * @param  array<string, mixed>  $options
     * @return array<string, mixed>
     */
    public function import(array $preview, array $options): array
    {
        $updateAcademic = (bool) ($options['update_academic_data'] ?? false);
        $updateNames = (bool) ($options['update_student_names'] ?? false);
        $note = $options['observations_note'] ?? null;

        $planPagoId = $this->intOrNull($options['plan_pago_id'] ?? null) ?: $this->ensureDefaultPlanPago();
        $representantId = $this->resolveRepresentant($options['representant_ci'] ?? null);

        $report = [
            'created' => 0,
            'inscribed' => 0,
            'updated' => 0,
            'ci_updated' => 0,
            'unchanged' => 0,
            'skipped' => 0,
            'discarded' => 0,
            'errors' => [],
            'plan_pago_id' => $planPagoId,
            'representant_id' => $representantId,
        ];

        foreach ($preview as $item) {
            if (($item['status'] ?? '') !== 'ok') {
                $report['skipped']++;

                continue;
            }

            // Paso 2: filas descartadas con el checkbox de la vista previa.
            if (array_key_exists('selected', $item) && ! $item['selected']) {
                $report['discarded']++;

                continue;
            }

            try {
                DB::transaction(function () use ($item, $options, $planPagoId, $representantId, $updateAcademic, $updateNames, $note, &$report) {
                    if ($item['estudiant_id']) {
                        $estudiant = Estudiant::find($item['estudiant_id']);
                    } elseif (! empty($item['ci_generated'])) {
                        // Los CI generados se conservan tal cual: búsqueda exacta.
                        $estudiant = Estudiant::where('ci_estudiant', (string) $item['ci'])->first();
                    } else {
                        $estudiant = $this->findEstudiantByCi((string) $item['ci']);
                    }

                    if (! $estudiant) {
                        $estudiant = Estudiant::create([
                            'ci_estudiant' => $item['ci'],
                            'lastname' => $item['lastname'] ?: null,
                            'name' => $item['name'] ?: null,
                            'planpago_id' => $planPagoId,
                            'representant_id' => $representantId,
                            'representant_ci' => (string) ($options['representant_ci'] ?? '') ?: '1111111111',
                            'type_ci_id' => 1,
                            'status_active' => 'true',
                            'status_blacklist' => 'false',
                        ]);
                        $report['created']++;
                    } else {
                        if (($item['action'] ?? '') === 'actualizar_ci') {
                            // CI actualizada (coincidencia por nombre, misma sección):
                            // solamente se actualiza la CI del estudiante.
                            if ((string) $estudiant->ci_estudiant !== (string) $item['ci']) {
                                $this->assertCiAvailable((string) $item['ci'], (int) $estudiant->id);
                                $estudiant->update(['ci_estudiant' => (string) $item['ci']]);
                                $report['ci_updated']++;
                            } else {
                                $report['unchanged']++;
                            }

                            return;
                        }

                        // Fila aceptada desde una coincidencia por nombre: la CI
                        // del CSV prevalece sobre la registrada.
                        if (! empty($item['name_match']) && (string) $estudiant->ci_estudiant !== (string) $item['ci']) {
                            $this->assertCiAvailable((string) $item['ci'], (int) $estudiant->id);
                            $estudiant->update(['ci_estudiant' => (string) $item['ci']]);
                            $report['ci_updated']++;
                        }

                        if ($updateNames && $this->nameChanges($estudiant, $item['lastname'], $item['name'])) {
                            $estudiant->update([
                                'lastname' => $item['lastname'] ?: $estudiant->lastname,
                                'name' => $item['name'] ?: $estudiant->name,
                            ]);
                        }
                    }

                    $inscripcion = Inscripcion::where('estudiant_id', $estudiant->id)->first();

                    if ($inscripcion) {
                        $attributes = [];
                        if ((int) $inscripcion->seccion_id !== (int) $item['seccion_id']) {
                            $attributes['seccion_id'] = $item['seccion_id'];
                        }
                        if ($updateAcademic) {
                            $attributes += $this->academicAttributes($inscripcion, $options);
                        }
                        if (! $inscripcion->observations && $note) {
                            $attributes['observations'] = $note;
                        }

                        if ($attributes) {
                            $inscripcion->update($attributes);
                            $report['updated']++;
                        } else {
                            $report['unchanged']++;
                        }

                        return;
                    }

                    Inscripcion::create([
                        'estudiant_id' => $estudiant->id,
                        'seccion_id' => $item['seccion_id'],
                        'tipo_id' => $options['tipo_id'],
                        'escolaridad_id' => $options['escolaridad_id'],
                        'programacion_id' => $options['programacion_id'],
                        'grupo_estable_id' => $this->intOrNull($options['grupo_estable_id'] ?? null),
                        'observations' => $note,
                    ]);
                    $report['inscribed']++;
                });
            } catch (\Throwable $e) {
                $report['errors'][] = [
                    'line' => $item['line'] ?? null,
                    'ci' => $item['ci'] ?? '',
                    'message' => $e->getMessage(),
                ];
                $report['skipped']++;
            }
        }

        return $report;
    }

    // ─── Resolución de entidades ───────────────────────────────

    public function findEstudiantByCi(string $ci): ?Estudiant
    {
        $ci = trim($ci);
        if ($ci === '') {
            return null;
        }

        $normalized = $this->normalizeCi($ci);

        $estudiant = Estudiant::where('ci_estudiant', $ci)->first();
        if ($estudiant) {
            return $estudiant;
        }

        if ($normalized !== $ci) {
            return Estudiant::where('ci_estudiant', $normalized)->first();
        }

        return null;
    }

    /**
     * Segundo criterio de coincidencia (el primero siempre es la CI): busca
     * estudiantes por [lastname, name] normalizados. Requiere ambos campos
     * no vacíos para evitar falsos positivos.
     *
     * @return array<int, Estudiant>
     */
    public function findEstudiantsByName(string $lastname, string $name): array
    {
        if ($this->normalizeFullName($lastname, $name) === '') {
            return [];
        }

        return $this->nameIndex()[$this->normalizeFullName($lastname, $name)] ?? [];
    }

    /**
     * Clave normalizada de [lastname, name]; '' si alguno viene vacío.
     */
    public function normalizeFullName(string $lastname, string $name): string
    {
        if (trim($lastname) === '' || trim($name) === '') {
            return '';
        }

        return $this->normalizeText(trim($lastname).' '.trim($name));
    }

    /** @return array<string, array<int, Estudiant>> */
    protected function nameIndex(): array
    {
        if ($this->nameIndex !== null) {
            return $this->nameIndex;
        }

        $map = [];
        foreach (Estudiant::query()->select(['id', 'ci_estudiant', 'name', 'lastname'])->get() as $estudiant) {
            $key = $this->normalizeFullName((string) $estudiant->lastname, (string) $estudiant->name);
            if ($key === '') {
                continue;
            }
            $map[$key][] = $estudiant;
        }

        return $this->nameIndex = $map;
    }

    /**
     * @throws \RuntimeException si la CI ya pertenece a otro estudiante.
     */
    protected function assertCiAvailable(string $ci, int $exceptId): void
    {
        $taken = Estudiant::where('ci_estudiant', $ci)
            ->where('id', '!=', $exceptId)
            ->exists();

        if ($taken) {
            throw new \RuntimeException("La CI {$ci} ya pertenece a otro estudiante.");
        }
    }

    public function resolveSeccion(string $gradoName, string $seccionName, ?int $pestudioId = null): ?Seccion
    {
        $targetGrado = $this->normalizeText($gradoName);
        $targetSeccion = $this->normalizeText($seccionName);

        $matches = $this->grados($pestudioId)->filter(function (Grado $grado) use ($targetGrado) {
            return $this->normalizeText((string) $grado->name) === $targetGrado
                || ($grado->code && $this->normalizeText((string) $grado->code) === $targetGrado);
        });

        if ($matches->count() !== 1) {
            return null;
        }

        return $this->secciones((int) $matches->first()->id)
            ->first(fn (Seccion $seccion) => $this->normalizeText((string) $seccion->name) === $targetSeccion);
    }

    /** @return Collection<int, Grado> */
    protected function grados(?int $pestudioId = null): Collection
    {
        if ($this->grados === null) {
            $this->grados = Grado::query()
                ->where('status_active', 'true')
                ->orderBy('id')
                ->get();
        }

        if ($pestudioId) {
            return $this->grados->where('pestudio_id', $pestudioId)->values();
        }

        return $this->grados;
    }

    /** @return Collection<int, Seccion> */
    protected function secciones(int $gradoId): Collection
    {
        if (! isset($this->seccionesPorGrado[$gradoId])) {
            $this->seccionesPorGrado[$gradoId] = Seccion::query()
                ->where('grado_id', $gradoId)
                ->where('status_active', 'true')
                ->orderBy('id')
                ->get();
        }

        return $this->seccionesPorGrado[$gradoId];
    }

    /** @param array<string, mixed> $options */
    protected function academicAttributes(Inscripcion $inscripcion, array $options): array
    {
        $attributes = [];

        $map = [
            'tipo_id' => 'tipo_id',
            'escolaridad_id' => 'escolaridad_id',
            'programacion_id' => 'programacion_id',
        ];

        foreach ($map as $column => $key) {
            $value = $this->intOrNull($options[$key] ?? null);
            if ($value && (int) $inscripcion->{$column} !== $value) {
                $attributes[$column] = $value;
            }
        }

        $grupo = $this->intOrNull($options['grupo_estable_id'] ?? null);
        if ((int) $inscripcion->grupo_estable_id !== (int) $grupo) {
            $attributes['grupo_estable_id'] = $grupo;
        }

        return $attributes;
    }

    /** @param array<string, mixed> $options */
    protected function academicChanges(Inscripcion $inscripcion, array $options): bool
    {
        return $this->academicAttributes($inscripcion, $options) !== [];
    }

    protected function nameChanges(Estudiant $estudiant, string $lastname, string $name): bool
    {
        $newLastname = $lastname !== '' ? $lastname : $estudiant->lastname;
        $newName = $name !== '' ? $name : $estudiant->name;

        return $newLastname !== $estudiant->lastname || $newName !== $estudiant->name;
    }

    // ─── Datos estándar ────────────────────────────────────────

    public function ensureDefaultPlanPago(): int
    {
        if ($this->defaultPlanPagoId !== null) {
            return $this->defaultPlanPagoId;
        }

        $existing = DB::table('planpagos')
            ->where('status_inscription_affects', 'true')
            ->where('status_active', 'true')
            ->orderBy('id')
            ->value('id')
            ?? DB::table('planpagos')->orderBy('id')->value('id');

        if ($existing) {
            return $this->defaultPlanPagoId = (int) $existing;
        }

        return $this->defaultPlanPagoId = (int) DB::table('planpagos')->insertGetId([
            'name' => 'PLAN ESTÁNDAR (IMPORTACIÓN)',
            'description' => 'Plan de pago por defecto para estudiantes creados por importación CSV.',
            'observations' => 'Creado automáticamente por el importador de inscripciones.',
            'status_active' => 'true',
            'enabled_for_administrative' => 'true',
            'status_cancel' => 'false',
            'status_inscription_affects' => 'true',
            'status_inscriptions' => 'true',
            'status_foreign_currency' => 'false',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    protected function resolveRepresentant(?string $ci): int
    {
        $ci = trim((string) $ci);

        if ($ci !== '') {
            $representant = Representant::where('ci_representant', $ci)->first();
            if ($representant) {
                return (int) $representant->id;
            }

            return (int) Representant::create([
                'ci_representant' => $ci,
                'name' => 'REPRESENTANTE (IMPORTACIÓN)',
                'status_active' => 'true',
                'status_blacklist' => 'false',
                'status_adviders' => 'false',
            ])->id;
        }

        return $this->ensureSentinelRepresentant();
    }

    public function ensureSentinelRepresentant(): int
    {
        if ($this->sentinelRepresentantId !== null) {
            return $this->sentinelRepresentantId;
        }

        $existing = Representant::where('ci_representant', '1111111111')->first();
        if ($existing) {
            return $this->sentinelRepresentantId = (int) $existing->id;
        }

        return $this->sentinelRepresentantId = (int) Representant::create([
            'ci_representant' => '1111111111',
            'name' => 'SIN REPRESENTANTE',
            'status_active' => 'true',
            'status_blacklist' => 'false',
            'status_adviders' => 'false',
        ])->id;
    }

    /** @return array<int, string> */
    public function planPagoOptions(): array
    {
        return DB::table('planpagos')
            ->orderBy('name')
            ->pluck('name', 'id')
            ->toArray();
    }

    // ─── Normalización ─────────────────────────────────────────

    public function normalizeCi(string $ci): string
    {
        $ci = trim($ci);
        $digits = preg_replace('/\D+/', '', $ci);

        return $digits !== '' ? $digits : mb_strtoupper($ci);
    }

    /**
     * Normaliza un CI generado (fila sin cédula real): conserva caracteres
     * alfanuméricos en mayúsculas y descarta separadores/espacios.
     */
    public function normalizeGeneratedCi(string $ci): string
    {
        $ci = mb_strtoupper(trim($ci));
        $ci = preg_replace('/[^A-Z0-9]+/', '', $ci);

        return $ci ?? '';
    }

    public function normalizeText(string $value): string
    {
        $value = mb_strtolower($this->toUtf8(trim($value)));
        $value = str_replace(
            ['á', 'é', 'í', 'ó', 'ú', 'ñ', 'ü', 'à', 'è', 'ì', 'ò', 'ù', 'â', 'ê', 'î', 'ô', 'û', 'ä', 'ë', 'ï', 'ö'],
            ['a', 'e', 'i', 'o', 'u', 'n', 'u', 'a', 'e', 'i', 'o', 'u', 'a', 'e', 'i', 'o', 'u', 'a', 'e', 'i', 'o'],
            $value
        );
        $value = preg_replace('/[^a-z0-9]+/', ' ', $value);

        return trim((string) preg_replace('/\s+/', ' ', $value));
    }

    public function normalizeHeader(string $value): string
    {
        return str_replace(' ', '_', $this->normalizeText($value));
    }

    public function toUtf8(string $value): string
    {
        if (str_starts_with($value, "\xEF\xBB\xBF")) {
            $value = substr($value, 3);
        }

        if ($value === '' || mb_check_encoding($value, 'UTF-8')) {
            return $value;
        }

        return mb_convert_encoding($value, 'UTF-8', 'Windows-1252');
    }

    // ─── Utilidades ────────────────────────────────────────────

    /** @param array<string, mixed> $item */
    protected function fail(array $item, string $message): array
    {
        $item['status'] = 'error';
        $item['message'] = $message;
        $item['selected'] = false;

        return $item;
    }

    protected function intOrNull(mixed $value): ?int
    {
        return ($value === null || $value === '') ? null : (int) $value;
    }
}
