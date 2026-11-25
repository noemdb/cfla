<?php

namespace App\Console\Commands;

use App\Services\OpenRouterService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class DbCorrectSpelling extends Command
{
    protected $signature = 'db:correct-spelling
        {table : Nombre de la tabla a analizar}
        {columns : Columnas textuales separadas por comas (ej: name,observations,prelacions)}
        {--dry-run : Solo analiza y muestra las correcciones propuestas sin modificar la BD}
        {--force : Aplica las correcciones sin solicitar confirmación interactiva}
        {--model= : Modelo específico de OpenRouter (por defecto usa la cadena de texto configurada)}
        {--batch-size=40 : Cantidad de valores únicos por lote enviado a OpenRouter (1-200)}
        {--limit= : Limitar la cantidad de valores únicos analizados por columna}
        {--connection= : Conexión de base de datos a utilizar}
        {--include-deleted : Incluir registros con soft-delete (deleted_at IS NOT NULL)}';

    protected $description = 'Analiza y corrige errores de ortografía y acentuación (tildes) en columnas textuales de una tabla usando OpenRouter';

    /**
     * Tipos de columna textuales permitidos para corrección ortográfica.
     *
     * @var list<string>
     */
    private const ALLOWED_TEXT_TYPES = [
        'varchar',
        'char',
        'string',
        'text',
        'tinytext',
        'mediumtext',
        'longtext',
        'character varying',
        'character',
        'nvarchar',
        'nchar',
        'clob',
    ];

    /**
     * Longitud máxima por defecto en bytes/caracteres para tipos TEXT de MySQL.
     *
     * @var array<string, int>
     */
    private const IMPLICIT_TEXT_MAX_LENGTH = [
        'tinytext' => 255,
        'text' => 65535,
        'mediumtext' => 16777215,
    ];

    public function handle(OpenRouterService $openRouter): int
    {
        $table = trim((string) $this->argument('table'));
        $rawColumns = (string) $this->argument('columns');
        $connection = $this->option('connection') ?: config('database.default');
        $dryRun = (bool) $this->option('dry-run');
        $force = (bool) $this->option('force');
        $includeDeleted = (bool) $this->option('include-deleted');

        // 1. Validar opciones numéricas
        $batchSize = filter_var($this->option('batch-size'), FILTER_VALIDATE_INT);
        if ($batchSize === false || $batchSize < 1 || $batchSize > 200) {
            $this->components->error('La opción --batch-size debe ser un número entero entre 1 y 200.');

            return Command::FAILURE;
        }

        $limit = null;
        if ($this->option('limit') !== null && $this->option('limit') !== '') {
            $parsedLimit = filter_var($this->option('limit'), FILTER_VALIDATE_INT);
            if ($parsedLimit === false || $parsedLimit < 1) {
                $this->components->error('La opción --limit debe ser un número entero mayor o igual a 1.');

                return Command::FAILURE;
            }
            $limit = $parsedLimit;
        }

        // 2. Validar API key de OpenRouter
        if (empty(config('openrouter.api_key'))) {
            $this->components->error('OPENROUTER_API_KEY no está configurada. Configúrala antes de ejecutar este comando.');

            return Command::FAILURE;
        }

        // 3. Validar nombre de tabla y existencia
        if (! $this->isSafeIdentifier($table)) {
            $this->components->error("Nombre de tabla inválido: '{$table}'.");

            return Command::FAILURE;
        }

        $schema = Schema::connection($connection);
        if (! $schema->hasTable($table)) {
            $this->components->error("La tabla '{$table}' no existe en la conexión '{$connection}'.");

            return Command::FAILURE;
        }

        // 4. Parsear y validar columnas
        $columns = array_values(array_unique(array_filter(
            array_map('trim', explode(',', $rawColumns)),
            static fn (string $col): bool => $col !== ''
        )));

        if ($columns === []) {
            $this->components->error('Debes indicar al menos una columna separada por comas.');

            return Command::FAILURE;
        }

        foreach ($columns as $col) {
            if (! $this->isSafeIdentifier($col)) {
                $this->components->error("Nombre de columna inválido: '{$col}'.");

                return Command::FAILURE;
            }
        }

        $metadata = $this->inspectColumns($connection, $table);
        $validatedColumns = [];
        $validationErrors = [];

        foreach ($columns as $col) {
            if (! isset($metadata[$col])) {
                $validationErrors[] = "La columna '{$col}' no existe en la tabla '{$table}'.";

                continue;
            }

            $info = $metadata[$col];

            if ($info['is_primary']) {
                $validationErrors[] = "La columna '{$col}' es clave primaria y no puede modificarse.";

                continue;
            }

            if (! in_array($info['base_type'], self::ALLOWED_TEXT_TYPES, true)) {
                $validationErrors[] = "La columna '{$col}' tiene tipo '{$info['raw_type']}', que no es textual (permitidos: varchar, char, string, text, tinytext, mediumtext, longtext).";

                continue;
            }

            $validatedColumns[$col] = $info;
        }

        if ($validationErrors !== []) {
            foreach ($validationErrors as $err) {
                $this->components->error($err);
            }

            return Command::FAILURE;
        }

        $hasDeletedAt = isset($metadata['deleted_at']);
        $models = $this->resolveModels();

        if ($models === []) {
            $this->components->error('No hay modelos de OpenRouter configurados.');

            return Command::FAILURE;
        }

        $this->components->info(sprintf(
            'Analizando tabla "%s" (conexión: %s) · columnas: %s%s',
            $table,
            $connection,
            implode(', ', array_keys($validatedColumns)),
            $dryRun ? ' [DRY-RUN]' : ''
        ));

        // 5. Recolectar valores únicos con comparación binaria exacta (respeta tildes en utf8mb4_unicode_ci)
        $plan = [];
        $totalProposedRows = 0;

        foreach ($validatedColumns as $column => $colInfo) {
            $valueCounts = $this->collectExactUniqueValues(
                $connection,
                $table,
                $column,
                $hasDeletedAt && ! $includeDeleted
            );

            $uniqueValues = array_keys($valueCounts);
            if ($limit !== null) {
                $uniqueValues = array_slice($uniqueValues, 0, $limit);
            }

            if ($uniqueValues === []) {
                $this->line("  [{$column}] Sin valores textuales no vacíos para analizar.");

                continue;
            }

            $this->line(sprintf(
                '  [%s] (%s%s) · %d valores únicos a analizar...',
                $column,
                $colInfo['raw_type'],
                $colInfo['max_length'] ? ", máx. {$colInfo['max_length']} chars" : '',
                count($uniqueValues)
            ));

            $chunks = array_chunk($uniqueValues, $batchSize);

            foreach ($chunks as $chunkIndex => $chunk) {
                $result = $this->analyzeChunkWithOpenRouter($openRouter, $models, $table, $column, $chunk);

                if (! $result['success']) {
                    $this->components->error(sprintf(
                        'Error al consultar OpenRouter en %s.%s (lote %d/%d): %s',
                        $table,
                        $column,
                        $chunkIndex + 1,
                        count($chunks),
                        $result['error']
                    ));

                    return Command::FAILURE;
                }

                foreach ($result['corrections'] as $original => $corrected) {
                    if ($colInfo['max_length'] !== null && mb_strlen($corrected) > $colInfo['max_length']) {
                        $this->components->warn(sprintf(
                            'Corrección omitida en %s.%s: supera el límite de %d caracteres ("%s" -> "%s").',
                            $table,
                            $column,
                            $colInfo['max_length'],
                            $original,
                            $corrected
                        ));

                        continue;
                    }

                    $affectedRows = $valueCounts[$original] ?? 0;
                    $plan[] = [
                        'column' => $column,
                        'original' => $original,
                        'corrected' => $corrected,
                        'rows' => $affectedRows,
                    ];
                    $totalProposedRows += $affectedRows;
                }
            }
        }

        if ($plan === []) {
            $this->components->info('No se detectaron errores de ortografía o acentuación.');

            return Command::SUCCESS;
        }

        // 6. Mostrar resumen de correcciones propuestas
        $this->newLine();
        $this->table(
            ['Columna', 'Original', 'Corregido', 'Filas'],
            array_map(
                static fn (array $item): array => [
                    $item['column'],
                    $item['original'],
                    $item['corrected'],
                    $item['rows'],
                ],
                $plan
            )
        );

        $this->components->twoColumnDetail('Correcciones únicas propuestas', (string) count($plan));
        $this->components->twoColumnDetail('Total de actualizaciones de campo estimadas', (string) $totalProposedRows);

        if ($dryRun) {
            $this->components->info('Modo --dry-run activo: no se realizaron modificaciones en la base de datos.');

            return Command::SUCCESS;
        }

        if (! $force && ! $this->confirm("¿Aplicar estas correcciones sobre la tabla '{$table}'?", false)) {
            $this->components->warn('Operación cancelada por el usuario.');

            return Command::SUCCESS;
        }

        // 7. Aplicar correcciones en transacción con comparación exacta
        $driver = DB::connection($connection)->getDriverName();
        $isMySql = in_array($driver, ['mysql', 'mariadb'], true);
        $updatedCount = 0;

        DB::connection($connection)->transaction(function () use (
            $connection,
            $table,
            $plan,
            $isMySql,
            $hasDeletedAt,
            $includeDeleted,
            &$updatedCount
        ): void {
            foreach ($plan as $item) {
                $column = $item['column'];
                $query = DB::connection($connection)->table($table);

                if ($hasDeletedAt && ! $includeDeleted) {
                    $query->whereNull('deleted_at');
                }

                if ($isMySql) {
                    $query->whereRaw("BINARY `{$column}` = ?", [$item['original']]);
                } else {
                    $query->where($column, $item['original']);
                }

                $updatedCount += $query->update([$column => $item['corrected']]);
            }
        });

        $this->components->info("Correcciones aplicadas correctamente. Actualizaciones de campo ejecutadas: {$updatedCount}.");

        return Command::SUCCESS;
    }

    /**
     * Valida que un nombre de tabla o columna sea un identificador SQL seguro.
     */
    private function isSafeIdentifier(string $identifier): bool
    {
        return preg_match('/^[a-zA-Z_][a-zA-Z0-9_]*$/', $identifier) === 1;
    }

    /**
     * Inspecciona columnas de la tabla devolviendo tipo bruto, tipo base,
     * longitud máxima (si aplica) y si forma parte de la clave primaria.
     *
     * @return array<string, array{raw_type: string, base_type: string, max_length: ?int, is_primary: bool}>
     */
    private function inspectColumns(string $connection, string $table): array
    {
        $db = DB::connection($connection);
        $driver = $db->getDriverName();
        $result = [];

        if (in_array($driver, ['mysql', 'mariadb'], true)) {
            $rows = $db->select("SHOW FULL COLUMNS FROM `{$table}`");

            foreach ($rows as $row) {
                $rawType = strtolower((string) $row->Type);
                [$baseType, $maxLength] = $this->parseTypeDetails($rawType);

                $result[(string) $row->Field] = [
                    'raw_type' => $rawType,
                    'base_type' => $baseType,
                    'max_length' => $maxLength,
                    'is_primary' => strtoupper((string) $row->Key) === 'PRI',
                ];
            }

            return $result;
        }

        if ($driver === 'sqlite') {
            $rows = $db->select("PRAGMA table_info(\"{$table}\")");

            foreach ($rows as $row) {
                $rawType = strtolower(trim((string) $row->type));
                [$baseType, $maxLength] = $this->parseTypeDetails($rawType);

                $result[(string) $row->name] = [
                    'raw_type' => $rawType,
                    'base_type' => $baseType,
                    'max_length' => $maxLength,
                    'is_primary' => (int) $row->pk > 0,
                ];
            }

            return $result;
        }

        // Fallback genérico (PostgreSQL u otros soportados por Schema)
        $columns = Schema::connection($connection)->getColumnListing($table);
        foreach ($columns as $col) {
            $rawType = strtolower((string) Schema::connection($connection)->getColumnType($table, $col));
            [$baseType, $maxLength] = $this->parseTypeDetails($rawType);

            $result[$col] = [
                'raw_type' => $rawType,
                'base_type' => $baseType,
                'max_length' => $maxLength,
                'is_primary' => $col === 'id',
            ];
        }

        return $result;
    }

    /**
     * Extrae tipo base y longitud máxima de una definición SQL como "varchar(191)".
     *
     * @return array{0: string, 1: ?int}
     */
    private function parseTypeDetails(string $rawType): array
    {
        $maxLength = null;

        if (preg_match('/^([a-z ]+?)\s*\(\s*(\d+)\s*\)/', $rawType, $matches) === 1) {
            $baseType = trim($matches[1]);
            $maxLength = (int) $matches[2];
        } else {
            $baseType = trim(preg_replace('/\(.*/', '', $rawType) ?? $rawType);
            // Remover modificadores como "unsigned"
            $baseType = trim(preg_replace('/\s+unsigned$/', '', $baseType) ?? $baseType);
        }

        if ($maxLength === null && isset(self::IMPLICIT_TEXT_MAX_LENGTH[$baseType])) {
            $maxLength = self::IMPLICIT_TEXT_MAX_LENGTH[$baseType];
        }

        return [$baseType, $maxLength];
    }

    /**
     * Recolecta valores únicos y su frecuencia usando comparación binaria exacta en PHP.
     *
     * @return array<string, int>
     */
    private function collectExactUniqueValues(
        string $connection,
        string $table,
        string $column,
        bool $excludeSoftDeleted
    ): array {
        $query = DB::connection($connection)
            ->table($table)
            ->select($column)
            ->whereNotNull($column)
            ->where($column, '!=', '');

        if ($excludeSoftDeleted) {
            $query->whereNull('deleted_at');
        }

        $counts = [];
        foreach ($query->cursor() as $row) {
            $value = (string) $row->{$column};
            if (trim($value) === '') {
                continue;
            }
            $counts[$value] = ($counts[$value] ?? 0) + 1;
        }

        ksort($counts);

        return $counts;
    }

    /**
     * Resuelve la cadena de modelos OpenRouter a intentar.
     *
     * @return list<string>
     */
    private function resolveModels(): array
    {
        $explicit = trim((string) ($this->option('model') ?? ''));
        if ($explicit !== '') {
            return [$explicit];
        }

        $candidates = [
            config('openrouter.model_text_primary'),
            config('openrouter.model_text_fallback1'),
            config('openrouter.model_text_fallback2'),
            config('openrouter.model_primary'),
            config('openrouter.model'),
        ];

        return array_values(array_unique(array_filter(
            array_map(static fn ($m): string => is_string($m) ? trim($m) : '', $candidates),
            static fn (string $m): bool => $m !== ''
        )));
    }

    /**
     * Envía un lote de valores únicos a OpenRouter con fallback entre modelos
     * y devuelve el mapa [original => corregido] solo para los valores modificados.
     *
     * @param  list<string>  $models
     * @param  list<string>  $values
     * @return array{success: bool, corrections: array<string, string>, model: ?string, error: ?string}
     */
    private function analyzeChunkWithOpenRouter(
        OpenRouterService $openRouter,
        array $models,
        string $table,
        string $column,
        array $values
    ): array {
        $indexedInput = [];
        foreach ($values as $idx => $val) {
            $indexedInput[(string) $idx] = $val;
        }

        $systemPrompt = implode("\n", [
            'Eres un corrector ortográfico experto en español (normativa RAE) para registros de bases de datos educativas.',
            'Tu única tarea es corregir errores de ortografía, acentuación (tildes), concordancia gramatical evidente, puntuación enumerativa faltante y espacios dobles.',
            'Reglas estrictas:',
            '1. Conserva exactamente el estilo de capitalización del texto original (si está en MAYÚSCULAS, devuelve el texto corregido en MAYÚSCULAS).',
            '2. NO cambies el significado, NO traduzcas, NO reescribas frases válidas ni cambies siglas, códigos, abreviaturas o números (ej. 1ER, 2DO, 1G, C.R.P.).',
            '3. Si un texto ya está correctamente escrito, NO lo incluyas en la salida (o devuélvelo idéntico).',
            '4. Responde ÚNICAMENTE con un objeto JSON válido donde cada clave sea el índice numérico recibido (como string) y su valor sea el texto corregido. Si ningún texto requiere corrección, responde exactamente {}.',
        ]);

        $userPrompt = sprintf(
            "Tabla: %s\nColumna: %s\nTextos a revisar (JSON):\n%s",
            $table,
            $column,
            json_encode($indexedInput, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT)
        );

        $errors = [];

        foreach ($models as $model) {
            $response = $openRouter->ask($systemPrompt, $userPrompt, [
                'model' => $model,
                'temperature' => 0.0,
                'response_format' => ['type' => 'json_object'],
            ]);

            if (! ($response['success'] ?? false)) {
                $errors[] = "{$model}: ".($response['error'] ?? 'error desconocido');

                continue;
            }

            $parsed = $this->decodeJsonObject((string) $response['content']);
            if ($parsed === null) {
                $errors[] = "{$model}: respuesta no es un objeto JSON válido";

                continue;
            }

            $corrections = [];
            foreach ($parsed as $key => $corrected) {
                $strKey = (string) $key;
                if (! array_key_exists($strKey, $indexedInput)) {
                    continue;
                }
                if (! is_string($corrected) || trim($corrected) === '') {
                    continue;
                }

                $original = $indexedInput[$strKey];
                if ($corrected !== $original) {
                    $corrections[$original] = $corrected;
                }
            }

            return [
                'success' => true,
                'corrections' => $corrections,
                'model' => $response['model'] ?? $model,
                'error' => null,
            ];
        }

        return [
            'success' => false,
            'corrections' => [],
            'model' => null,
            'error' => implode(' | ', $errors),
        ];
    }

    /**
     * Decodifica un objeto JSON tolerando bloques markdown ```json ... ```.
     *
     * @return array<string|int, mixed>|null
     */
    private function decodeJsonObject(string $raw): ?array
    {
        $clean = trim($raw);

        if (preg_match('/```(?:json)?\s*(\{.*\})\s*```/s', $clean, $matches) === 1) {
            $clean = $matches[1];
        } elseif (! str_starts_with($clean, '{')) {
            $start = strpos($clean, '{');
            $end = strrpos($clean, '}');
            if ($start !== false && $end !== false && $end > $start) {
                $clean = substr($clean, $start, $end - $start + 1);
            }
        }

        $decoded = json_decode($clean, true);

        return is_array($decoded) ? $decoded : null;
    }
}
