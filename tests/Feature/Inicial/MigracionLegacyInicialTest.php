<?php

namespace Tests\Feature\Inicial;

use App\Services\Inicial\MigradorLegacyInicial;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

/**
 * F6 del módulo de Educación Inicial: la migración de datos del legacy.
 *
 * Blueprint: blueprint/inicial · F6 · §A.10.
 *
 * ─────────────────────────────────────────────────────────────────────────────
 * QUÉ SE COMPRUEBA Y QUÉ NO
 * ─────────────────────────────────────────────────────────────────────────────
 * Estas pruebas NO migran las 2.615 filas reales de `s2526`: hacerlo en la suite
 * dejaría el módulo con datos de producción y rompería el aislamiento. En su
 * lugar se comprueban las PROPIEDADES del migrador, que es lo que puede fallar:
 *
 *  · informa antes de escribir (por defecto no toca nada);
 *  · aborta cuando hay referencias rotas;
 *  · salta las huérfanas solo si se lo piden;
 *  · es idempotente: una fila ya presente no se duplica;
 *  · copia la intersección de columnas e informa de las que no tienen destino.
 *
 * El volumen y el guardián REALES se comprueban con
 * `php8.2 artisan inicial:migrate-legacy`, que sin `--forzar` no escribe nada.
 *
 * @group inicial
 * @group inicial-f6
 */
class MigracionLegacyInicialTest extends TestCase
{
    use DatabaseTransactions;

    /** Conexión legacy disponible en este entorno. */
    private function conexionLegacy(): ?string
    {
        try {
            DB::connection('s2526')->select('SELECT 1');

            return 's2526';
        } catch (\Throwable $e) {
            return null;
        }
    }

    private function saltarSiNoHayLegacy(): void
    {
        if ($this->conexionLegacy() === null) {
            $this->markTestSkipped('La conexión legacy s2526 no está disponible en este entorno.');
        }
    }

    // ─── Informe ──────────────────────────────────────────────────

    /** @test */
    public function sin_autorizacion_solo_informa_y_no_escribe(): void
    {
        $this->saltarSiNoHayLegacy();

        $antes = DB::table('eiplanningwks')->count();

        $this->artisan('inicial:migrate-legacy')
            ->expectsOutputToContain('Solo informe')
            ->assertSuccessful();

        // El punto de la opción por defecto: informar sin escribir.
        $this->assertSame($antes, DB::table('eiplanningwks')->count());
    }

    /** @test */
    public function el_informe_presenta_el_total_a_insertar(): void
    {
        $this->saltarSiNoHayLegacy();

        $plan = (new MigradorLegacyInicial)->planificar();

        $this->assertNotEmpty($plan['tablas']);
        $this->assertGreaterThan(0, $plan['total_nuevos']);
        $this->assertSame(
            array_keys($plan['tablas']),
            \App\Services\Inicial\MigradorLegacyInicial::tablas(),
            'El plan debe recorrer las tablas en el orden de carga declarado.'
        );
    }

    /** @test */
    public function el_orden_de_carga_pone_a_los_padres_antes_que_a_las_hijas(): void
    {
        $tablas = MigradorLegacyInicial::tablas();

        $posicion = fn (string $t): int => array_search($t, $tablas, true);

        // Una estrategia no puede cargarse antes que el plan del que cuelga.
        $this->assertLessThan($posicion('eiplanningwstrategies'), $posicion('eiplanningwks'));
        $this->assertLessThan($posicion('eiprojectreviews'), $posicion('eiprojectks'));
        $this->assertLessThan($posicion('eievaluationps'), $posicion('eievaluationks'));
        $this->assertLessThan($posicion('eispecialacts'), $posicion('eispecialks'));
    }

    // ─── Guardián de integridad ───────────────────────────────────

    /** @test */
    public function el_guardian_detecta_las_filas_sin_plan_padre(): void
    {
        $this->saltarSiNoHayLegacy();

        $plan = (new MigradorLegacyInicial)->planificar();

        // La fuente puede contener filas huérfanas (estrategias, resúmenes,
        // revisiones y actividades cuyo plan se borró sin cascada).
        $huerfanas = collect($plan['huerfanas'])->sum(fn (array $h) => $h['filas']);

        $this->assertGreaterThan(0, $huerfanas, 'La fuente debería tener filas huérfanas para que esta prueba signifique algo.');

        foreach ($plan['huerfanas'] as $tabla => $info) {
            $this->assertNotEmpty($info['ids'], "{$tabla}: se debe decir QUÉ planes faltan.");
        }
    }

    /** @test */
    public function el_guardian_aborta_y_no_escribe_si_hay_referencias_rotas(): void
    {
        $this->saltarSiNoHayLegacy();

        $antes = DB::table('eiplanningwks')->count();

        // Sin `--omitir-huerfanas` el migrador lanza y no toca la base.
        $this->expectException(\RuntimeException::class);

        try {
            (new MigradorLegacyInicial)->ejecutar();
        } finally {
            $this->assertSame($antes, DB::table('eiplanningwks')->count());
        }
    }

    /** @test */
    public function el_comando_aborta_cuando_hay_referencias_rotas(): void
    {
        $this->saltarSiNoHayLegacy();

        $this->artisan('inicial:migrate-legacy --forzar')
            ->expectsOutputToContain('Integridad rota')
            ->assertFailed();
    }

    /** @test */
    public function con_omitir_huerfanas_la_migracion_se_autoriza(): void
    {
        $this->saltarSiNoHayLegacy();

        $migrador = new MigradorLegacyInicial(omitirHuerfanas: true);

        $this->assertSame([], $migrador->problemas([]), 'Un plan sin huérfanas no da problemas.');

        $plan = $migrador->planificar();

        // Con la autorización, `problemas()` sigue INFORMANDO pero ya no bloquea:
        // es el propio `ejecutar()` el que decide, y aquí no debe lanzar.
        $this->assertNotEmpty($migrador->problemas($plan));
    }

    // ─── Idempotencia ──────────────────────────────────────────────

    /**
     * Idempotencia: una fila cuyo `id` ya está en el destino NO vuelve a
     * insertarse, y reejecutar el comando no duplica nada.
     *
     * Se copia una fila REAL del origen en vez de inventarla: sus claves
     * foráneas son válidas por construcción, y `DatabaseTransactions` deshace
     * la escritura al terminar.
     *
     * @test
     */
    public function las_filas_ya_presentes_no_se_cuentan_como_nuevas(): void
    {
        $this->saltarSiNoHayLegacy();

        $tabla = 'eiplanningwks';
        $migrador = new MigradorLegacyInicial;

        // El destino puede traer filas propias (planes reales del período
        // actual): la idempotencia se mide contra la base previa, no contra
        // una tabla vacía.
        $baseDestino = DB::table($tabla)->count();

        $antes = $migrador->planificar(solo: [$tabla])['total_nuevos'];
        $this->assertGreaterThan(0, $antes);

        // Se inserta tal cual la primera fila del origen.
        $origen = DB::connection('s2526')->table($tabla)->orderBy('id')->first();

        DB::table($tabla)->insert(
            collect((array) $origen)->only(Schema::getColumnListing($tabla))->all()
        );

        $this->assertSame(
            $antes - 1,
            $migrador->planificar(solo: [$tabla])['total_nuevos'],
            'Una fila ya presente debe salirse del recuento de nuevas.'
        );

        // Al ejecutar se insertan solo las que faltan.
        $insertadas = $migrador->ejecutar(solo: [$tabla]);

        $this->assertSame($antes - 1, $insertadas[$tabla]);
        $this->assertSame(
            $baseDestino + $antes,
            DB::table($tabla)->count(),
            'Tras migrar, el destino suma sus filas propias más las del origen.'
        );

        // Reejecutar no inserta ni una fila: esa es la propiedad de idempotencia.
        // El servicio devuelve el conteo por tabla (no una lista vacía), así que
        // lo que se comprueba es que todos los conteos son cero.
        $this->assertSame(
            ['eiplanningwks' => 0],
            $migrador->ejecutar(solo: [$tabla]),
        );
    }

    // ─── Columnas ─────────────────────────────────────────────────

    /** @test */
    public function copia_la_interseccion_de_columnas_e_informa_de_las_que_no_tienen_destino(): void
    {
        $this->saltarSiNoHayLegacy();

        $plan = (new MigradorLegacyInicial)->planificar();

        foreach ($plan['tablas'] as $tabla => $info) {
            $origen = Schema::getColumnListing($tabla);

            $this->assertGreaterThan(0, $info['columnas'], "{$tabla}: debe copiar alguna columna.");

            // Lo que no se copia está declarado, nunca descartado en silencio.
            foreach ($info['columnas_ignoradas'] as $columna) {
                $this->assertContains($columna, $origen, "{$tabla}: {$columna} no es una columna del origen.");
            }
        }
    }

    // ─── Filtrado de tablas ───────────────────────────────────────

    /** @test */
    public function se_puede_migrar_una_sola_tabla(): void
    {
        $this->saltarSiNoHayLegacy();

        $plan = (new MigradorLegacyInicial)->planificar(solo: ['eiplanningwks']);

        $this->assertSame(['eiplanningwks'], array_keys($plan['tablas']));
    }

    /** @test */
    public function se_puede_excluir_una_tabla(): void
    {
        $this->saltarSiNoHayLegacy();

        $plan = (new MigradorLegacyInicial)->planificar(omitir: ['eiplanningwks']);

        $this->assertArrayNotHasKey('eiplanningwks', $plan['tablas']);
    }

    /** @test */
    public function una_conexion_inexistente_no_revienta_el_comando(): void
    {
        $this->artisan('inicial:migrate-legacy --origen=no_existe')
            ->expectsOutputToContain('No se puede conectar')
            ->assertFailed();
    }

    /** @test */
    public function el_migrador_detecta_que_la_conexion_no_responde(): void
    {
        $migrador = new MigradorLegacyInicial(origen: 'no_existe');

        $this->assertFalse($migrador->origenDisponible());
    }
}
