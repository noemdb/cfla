<?php

namespace App\Console\Commands;

use App\Services\Inicial\MigradorLegacyInicial;
use Illuminate\Console\Command;
use Throwable;

/**
 * Migra los datos del módulo de Educación Inicial desde la conexión legacy.
 *
 * ─────────────────────────────────────────────────────────────────────────────
 * POR DEFECTO NO ESCRIBE NADA
 * ─────────────────────────────────────────────────────────────────────────────
 * Sin `--forzar` el comando solo INFORMA: qué tablas hay, cuántas filas se
 * copiarían, cuáles ya existen, qué referencias están rotas y qué columnas del
 * origen no tienen destino. Escribir exige `--forzar` además de pasar el
 * guardián, porque el proyecto está en producción y la regla es no tocar datos
 * sin consentimiento explícito.
 *
 * ─────────────────────────────────────────────────────────────────────────────
 * NUNCA ES DESTRUCTIVO
 * ─────────────────────────────────────────────────────────────────────────────
 * Solo `INSERT`. No hay DROP, TRUNCATE, DELETE ni `migrate:fresh` en ninguna
 * rama, ni siquiera con `--forzar`. La fuente es solo lectura y las filas cuyo
 * `id` ya existe en el destino se saltan, así que el comando es reejecutable.
 *
 * ─────────────────────────────────────────────────────────────────────────────
 * EJEMPLOS
 * ─────────────────────────────────────────────────────────────────────────────
 *   php8.2 artisan inicial:migrate-legacy                       # informe
 *   php8.2 artisan inicial:migrate-legacy --omitir-huerfanas     # solo informe
 *   php8.2 artisan inicial:migrate-legacy --forzar               # escribe
 *   php8.2 artisan inicial:migrate-legacy --forzar --solo=eiplanningwks
 *   php8.2 artisan inicial:migrate-legacy --simular --forzar     # no escribe
 */
class MigrateLegacyInicial extends Command
{
    protected $signature = 'inicial:migrate-legacy
        {--origen=s2526 : Conexión legacy de la que leer}
        {--solo= : Lista de tablas a migrar, separadas por comas}
        {--omitir= : Lista de tablas a excluir, separadas por comas}
        {--chunks=500 : Filas por lote}
        {--omitir-huerfanas : No abortar si hay filas sin plan padre; se saltan y se registran}
        {--simular : Con --forzar, recorre todo el trabajo pero sin escribir}
        {--forzar : Autoriza la escritura (sin esta opción el comando solo informa)}';

    protected $description = 'Migra los datos de Educación Inicial desde la conexión legacy (solo INSERT, idempotente)';

    public function handle(): int
    {
        $migrador = new MigradorLegacyInicial(
            origen: (string) $this->option('origen'),
            chunks: max(1, (int) $this->option('chunks')),
            omitirHuerfanas: (bool) $this->option('omitir-huerfanas'),
        );

        if (! $migrador->origenDisponible()) {
            $this->error("No se puede conectar a «{$this->option('origen')}». Revise DB_DATABASE_S2526 en .env.");

            return self::FAILURE;
        }

        try {
            $plan = $migrador->planificar($this->lista($this->option('solo')), $this->lista($this->option('omitir')));
        } catch (Throwable $e) {
            $this->error('No se pudo planificar la migración: '.$e->getMessage());

            return self::FAILURE;
        }

        $this->informe($plan);

        $forzar = (bool) $this->option('forzar');
        $simular = (bool) $this->option('simular');

        if (! $forzar) {
            $this->newLine();
            $this->line('  <comment>Solo informe: nada se ha escrito.</comment> Añada <info>--forzar</info> para migrar.');

            return self::SUCCESS;
        }

        if ($plan['total_nuevos'] === 0) {
            $this->newLine();
            $this->info('No hay filas nuevas que migrar: el destino ya está al día.');

            return self::SUCCESS;
        }

        if ($simular) {
            $this->newLine();
            $this->line('  <comment>--simular: se recorre el trabajo sin escribir.</comment>');

            return self::SUCCESS;
        }

        $this->newLine();
        $this->line('  <info>Migrando '.$plan['total_nuevos'].' fila(s)…</info>');

        try {
            $insertadas = $migrador->ejecutar(
                $this->lista($this->option('solo')),
                $this->lista($this->option('omitir')),
                function (string $tabla, int $total): void {
                    $this->line("    <comment>{$tabla}</comment>: {$total} fila(s)");
                }
            );
        } catch (Throwable $e) {
            $this->newLine();
            $this->error($e->getMessage());

            return self::FAILURE;
        }

        $this->newLine();
        $this->info('Migración terminada.');
        $this->table(['Tabla', 'Filas insertadas'], collect($insertadas)->map(fn ($n, $t) => [$t, (string) $n])->values()->all());

        return self::SUCCESS;
    }

    /**
     * @param  array<string, mixed>  $plan
     */
    private function informe(array $plan): void
    {
        $this->newLine();
        $this->line('  <info>Migración de datos · Educación Inicial</info>');
        $this->line('  <comment>solo INSERT · idempotente por id · nada se borra</comment>');

        if ($plan['tablas'] === []) {
            $this->newLine();
            $this->warn('No hay tablas que migrar con esos filtros.');

            return;
        }

        $this->newLine();
        $this->table(
            ['Tabla', 'En origen', 'Nuevos', 'Ya migradas', 'Columnas'],
            collect($plan['tablas'])->map(fn (array $i, string $t) => [
                $t,
                (string) $i['origen'],
                (string) $i['nuevos'],
                (string) $i['existentes'],
                (string) $i['columnas'],
            ])->values()->all()
        );

        $this->line('  <info>Total a insertar: '.$plan['total_nuevos'].'</info>');

        if ($plan['columnas_faltantes'] !== []) {
            $this->newLine();
            $this->line('  <comment>Columnas del origen SIN destino (no se copian):</comment>');
            foreach ($plan['columnas_faltantes'] as $tabla => $columnas) {
                $this->line("    {$tabla}: ".implode(', ', $columnas));
            }
        }

        if ($plan['huerfanas'] !== []) {
            $this->newLine();
            $this->line('  <error>Filas sin plan padre en la fuente:</error>');
            foreach ($plan['huerfanas'] as $tabla => $h) {
                $this->line(sprintf(
                    '    <error>%s: %d fila(s)</error> <comment>(padres %s: %s)</comment>',
                    $tabla,
                    $h['filas'],
                    $h['fk'],
                    implode(', ', $h['ids']) ?: '—'
                ));
            }
        }

        if ($plan['claves_roto'] !== []) {
            $this->newLine();
            $this->line('  <error>Referencias que no existen en cfla:</error>');
            foreach ($plan['claves_roto'] as $clave => $cuantas) {
                $this->line("    <error>{$clave}: {$cuantas}</error>");
            }
        }
    }

    /**
     * @return array<int, string>
     */
    private function lista(mixed $valor): array
    {
        if (! is_string($valor) || trim($valor) === '') {
            return [];
        }

        return array_values(array_filter(array_map('trim', explode(',', $valor))));
    }
}
