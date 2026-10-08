<?php

namespace Tests\Feature\Profesor;

use App\Livewire\Profesor\Performance\PerformanceReport;
use App\Models\User;
use App\Models\app\Academy\Grado;
use App\Models\app\Academy\Pensum;
use App\Models\app\Academy\Pestudio;
use App\Models\app\Academy\Seccion;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Livewire\Livewire;
use Tests\TestCase;

/**
 * El payload JSON del reporte "Mi desempeño" lleva la clave raíz
 * `estructura` (constante, sin cálculo) y cada ruta `fuente`/`campo`
 * declarada en ella debe resolver en el JSON generado.
 */
class PerformanceEstructuraTest extends TestCase
{
    use DatabaseTransactions;

    public function test_estructura_modulo_es_exacta(): void
    {
        $user = $this->createProfesorUser();

        $this->actingAs($user);

        $payload = Livewire::test(PerformanceReport::class)
            ->assertOk()
            ->viewData('payload');

        $this->assertSame(
            'Mod de Planificacion, Desempeno Docente',
            $payload['estructura']['modulo']
        );
        $this->assertSame(
            PerformanceReport::ESTRUCTURA,
            $payload['estructura']
        );
        $this->assertSame(
            [
                'modo' => 'light',
                'disposicion' => 'bento compacto de una sola vista',
                'scroll_vertical' => true,
                'bloquear_scroll' => false,
                'todo_visible' => true,
                'imprimible' => true,
            ],
            array_intersect_key(
                $payload['estructura']['presentacion'],
                array_flip(['modo', 'disposicion', 'scroll_vertical', 'bloquear_scroll', 'todo_visible', 'imprimible'])
            )
        );
    }

    public function test_claves_existentes_no_cambian(): void
    {
        $user = $this->createProfesorUser();

        $this->actingAs($user);

        $payload = Livewire::test(PerformanceReport::class)
            ->assertOk()
            ->viewData('payload');

        $this->assertSame(
            [
                'reporte', 'mode', 'tarea', 'generado_en', 'profesor', 'rango', 'metodologia',
                'carga_academica', 'actividades_planificadas', 'actividades_registradas',
                'calidad_detalle_palabras', 'lecciones_lms', 'horario', 'bitacora',
                'notificaciones', 'por_area_seccion', 'instruccion_ia', 'instruccion_html', 'estructura',
            ],
            array_keys($payload)
        );
        $this->assertSame('Desempeño docente (Mi desempeño)', $payload['reporte']);
        $this->assertSame('light', $payload['mode']);
        $this->assertStringContainsString('única fuente', $payload['tarea']);
        $this->assertStringContainsString('Tailwind', $payload['instruccion_html']);
        $this->assertStringContainsString('estructura', $payload['instruccion_html']);
        $this->assertStringContainsString('página web completa', $payload['instruccion_html']);
    }

    public function test_todas_las_fuentes_de_estructura_resuelven(): void
    {
        [$user, $desde, $hasta] = $this->createProfesorConActividades();

        $this->actingAs($user);

        $payload = Livewire::test(PerformanceReport::class)
            ->set('desde', $desde)
            ->set('hasta', $hasta)
            ->assertOk()
            ->viewData('payload');

        // Cifras existentes fluyen intactas al payload.
        $this->assertSame(2, $payload['actividades_planificadas']['total']);
        $this->assertSame(1, $payload['actividades_planificadas']['aprobadas']);
        $this->assertSame(1, $payload['actividades_planificadas']['en_revision']);

        $estructura = $payload['estructura'];
        $errores = [];

        $check = function (string $path) use ($payload, &$errores): mixed {
            [$ok, $value] = $this->resolvePath($payload, $path);
            if (!$ok) {
                $errores[] = "fuente no resuelve: {$path}";
            }

            return $value;
        };

        foreach ($estructura['secciones'] as $seccion) {
            $id = $seccion['id'] ?? '?';

            if (isset($seccion['titulo']) && is_array($seccion['titulo'])) {
                $check($seccion['titulo']['fuente']);
            }
            if (isset($seccion['destacado']['fuente'])) {
                $check($seccion['destacado']['fuente']);
            }
            foreach ($seccion['datos'] ?? [] as $dato) {
                foreach ((array) ($dato['fuente'] ?? []) as $ruta) {
                    $check($ruta);
                }
            }
            foreach ($seccion['indicadores'] ?? [] as $ind) {
                $check($ind['fuente']);
                if (isset($ind['total'])) {
                    $check($ind['total']);
                }
            }
            // Listas simples: fuente es array (de filas o mapa).
            if (isset($seccion['fuente']) && !isset($seccion['fuentes'])) {
                $lista = $check($seccion['fuente']);
                $this->assertIsArray($lista, "fuente {$seccion['fuente']} debe ser arreglo");
                $this->assertCamposEnFilas($id, $lista, $seccion, $errores);
            }
            // Unión de listas (detalle de actividades): cada campo debe
            // existir en al menos una fila de la unión; `id` en todas.
            if (isset($seccion['fuentes'])) {
                $union = [];
                foreach ($seccion['fuentes'] as $rutaLista) {
                    $lista = $check($rutaLista);
                    $this->assertIsArray($lista, "fuente {$rutaLista} debe ser arreglo");
                    foreach ($lista as $fila) {
                        $union[] = $fila;
                    }
                }
                if ($union !== []) {
                    foreach ($union as $fila) {
                        if (!array_key_exists('id', $fila)) {
                            $errores[] = "sección {$id}: fila sin id en la unión";
                        }
                    }
                    foreach ($seccion['columnas'] as $col) {
                        $existe = collect($union)->contains(fn ($fila) => array_key_exists($col['campo'], $fila));
                        if (!$existe) {
                            $errores[] = "sección {$id}: campo {$col['campo']} en ninguna fila de la unión";
                        }
                    }
                }
            }
            foreach ($seccion['items'] ?? [] as $item) {
                $mapa = $check($seccion['fuente']);
                if (is_array($mapa) && !array_key_exists($item['campo'], $mapa)) {
                    $errores[] = "sección {$id}: campo {$item['campo']} ausente en {$seccion['fuente']}";
                }
            }
            foreach ($seccion['resumen'] ?? [] as $dato) {
                $check($dato['fuente']);
            }
            foreach ($seccion['complemento'] ?? [] as $dato) {
                $check($dato['fuente']);
            }
            if (isset($seccion['total'])) {
                $check($seccion['total']);
            }
            if (isset($seccion['etiqueta_superior'])) {
                if (!array_key_exists($seccion['etiqueta_superior'], $estructura)) {
                    $errores[] = "etiqueta_superior no resuelve: {$seccion['etiqueta_superior']}";
                }
            }
        }

        $this->assertSame([], $errores, "Rutas sin resolver:\n".implode("\n", $errores));
    }

    public function test_listas_vacias_no_rompen_el_reporte(): void
    {
        $user = $this->createProfesorUser();

        $this->actingAs($user);

        $payload = Livewire::test(PerformanceReport::class)
            ->assertOk()
            ->viewData('payload');

        $this->assertSame(0, $payload['actividades_planificadas']['total']);
        $this->assertSame([], $payload['actividades_planificadas']['detalle']);
        $this->assertSame([], $payload['horario']['detalle']);
        $this->assertSame([], $payload['por_area_seccion']);
        $this->assertSame(
            'Mod de Planificacion, Desempeno Docente',
            $payload['estructura']['modulo']
        );
    }

    /**
     * El JSON trae TODAS las activities (sin límite): con 17 actividades, el
     * detalle trae 17 ordenadas por relevancia (aprobada primero) y las
     * reglas declaran el visible top 15.
     */
    public function test_json_trae_todas_y_visible_solo_top_15(): void
    {
        [$user, $desde, $hasta] = $this->createProfesorConMuchasActividades(17);

        $this->actingAs($user);

        $payload = Livewire::test(PerformanceReport::class)
            ->set('desde', $desde)
            ->set('hasta', $hasta)
            ->assertOk()
            ->viewData('payload');

        $this->assertSame(17, $payload['actividades_planificadas']['total']);
        $this->assertCount(17, $payload['actividades_planificadas']['detalle']);
        $this->assertCount(17, $payload['actividades_registradas']['detalle']);
        $this->assertSame(
            'Aprobada',
            $payload['actividades_planificadas']['detalle'][0]['estado']
        );
        $this->assertArrayHasKey('palabras', $payload['actividades_planificadas']['detalle'][0]);
        $this->assertStringContainsString(
            '15',
            $payload['estructura']['presentacion']['reglas']
        );
        $this->assertArrayHasKey('relevancia', $payload['metodologia']);
    }

    public function test_visible_obliga_hasta_15_actividades(): void
    {
        [$user, $desde, $hasta] = $this->createProfesorConMuchasActividades(17);

        $this->actingAs($user);

        $payload = Livewire::test(PerformanceReport::class)
            ->set('desde', $desde)
            ->set('hasta', $hasta)
            ->assertOk()
            ->viewData('payload');

        $this->assertSame(15, $payload['estructura']['presentacion']['max_actividades_visibles']);
        $detalle = collect($payload['estructura']['secciones'])->firstWhere('id', 'detalle_actividades');
        $this->assertNotNull($detalle);
        $this->assertSame(15, $detalle['limite_visible']);
    }

    /**
     * Columnas de tabla/lista sobre una fuente: cada campo debe existir en
     * cada fila (mapas) o en cada fila de la lista.
     */
    private function assertCamposEnFilas(string $id, mixed $lista, array $seccion, array &$errores): void
    {
        if (!is_array($lista)) {
            return;
        }
        $columnas = $seccion['columnas'] ?? [];
        if ($columnas === [] || $lista === []) {
            return;
        }
        $esMapa = array_keys($lista) !== range(0, count($lista) - 1);
        if ($esMapa) {
            foreach ($columnas as $col) {
                if (!array_key_exists($col['campo'], $lista)) {
                    $errores[] = "sección {$id}: campo {$col['campo']} ausente";
                }
            }

            return;
        }
        foreach ($lista as $i => $fila) {
            foreach ($columnas as $col) {
                if (!is_array($fila) || !array_key_exists($col['campo'], $fila)) {
                    $errores[] = "sección {$id}: campo {$col['campo']} ausente en fila {$i}";
                }
            }
        }
    }

    /** @return array{0: bool, 1: mixed} */
    private function resolvePath(array $data, string $path): array
    {
        $actual = $data;
        foreach (explode('.', $path) as $segmento) {
            if (!is_array($actual) || !array_key_exists($segmento, $actual)) {
                return [false, null];
            }
            $actual = $actual[$segmento];
        }

        return [true, $actual];
    }

    private function createProfesorUser(?int $profesorId = null): User
    {
        $user = User::factory()->create(['is_profesor' => true]);

        $profesorId ??= DB::table('profesors')->insertGetId([
            'name' => 'Docente Estructura',
            'status_active' => 'true',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        DB::table('profesors')->where('id', $profesorId)->update(['user_id' => $user->id]);

        return $user;
    }

    /** @return array{0: User, 1: string, 2: string} */
    private function createProfesorConActividades(): array
    {
        $pestudio = Pestudio::factory()->create(['status_active' => 'true']);
        $grado = Grado::factory()->create(['status_active' => 'true']);
        $pensum = Pensum::factory()->create([
            'pestudio_id' => $pestudio->id,
            'grado_id' => $grado->id,
            'status_active' => 1,
        ]);
        $seccion = Seccion::factory()->create([
            'grado_id' => $grado->id,
            'status_active' => 'true',
        ]);

        $lapsoId = DB::table('lapsos')->insertGetId([
            'code' => 'LAP-EST-1',
            'code_sm' => 'LE',
            'name' => 'Lapso Estructura',
            'finicial' => now()->subMonth(),
            'ffinal' => now()->addMonth(),
            'status_last' => 'true',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $profesorId = DB::table('profesors')->insertGetId([
            'name' => 'Docente Estructura Act',
            'status_active' => 'true',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $user = $this->createProfesorUser($profesorId);

        $pevaluacionId = DB::table('pevaluacions')->insertGetId([
            'pensum_id' => $pensum->id,
            'profesor_id' => $profesorId,
            'lapso_id' => $lapsoId,
            'seccion_id' => $seccion->id,
            'objetivo' => 'Objetivo estructura',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $finicial = now()->subDays(2)->toDateString();
        $creada = now()->subDay()->setTime(12, 0)->toDateTimeString();

        DB::table('activities')->insert([
            'pevaluacion_id' => $pevaluacionId,
            'finicial' => $finicial,
            'ffinal' => $finicial,
            'topic' => '  Tema estructura uno  ',
            'teaching' => 'Enseñanza planificada con actividades claras para todos los estudiantes del grupo durante toda la semana escolar completa',
            'description' => 'Evaluativo uno',
            'status' => true,
            'created_at' => $creada,
            'updated_at' => $creada,
        ]);
        DB::table('activities')->insert([
            'pevaluacion_id' => $pevaluacionId,
            'finicial' => $finicial,
            'ffinal' => $finicial,
            'topic' => 'Tema estructura dos',
            'teaching' => 'Texto corto',
            'description' => null,
            'status' => false,
            'created_at' => $creada,
            'updated_at' => $creada,
        ]);

        return [$user, now()->subDays(6)->toDateString(), now()->toDateString()];
    }

    /** @return array{0: User, 1: string, 2: string} */
    private function createProfesorConMuchasActividades(int $total): array
    {
        $pestudio = Pestudio::factory()->create(['status_active' => 'true']);
        $grado = Grado::factory()->create(['status_active' => 'true']);
        $pensum = Pensum::factory()->create([
            'pestudio_id' => $pestudio->id,
            'grado_id' => $grado->id,
            'status_active' => 1,
        ]);
        $seccion = Seccion::factory()->create([
            'grado_id' => $grado->id,
            'status_active' => 'true',
        ]);

        $lapsoId = DB::table('lapsos')->insertGetId([
            'code' => 'LAP-EST-N',
            'code_sm' => 'LN',
            'name' => 'Lapso Estructura N',
            'finicial' => now()->subMonth(),
            'ffinal' => now()->addMonth(),
            'status_last' => 'true',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $profesorId = DB::table('profesors')->insertGetId([
            'name' => 'Docente Estructura N',
            'status_active' => 'true',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $user = $this->createProfesorUser($profesorId);

        $pevaluacionId = DB::table('pevaluacions')->insertGetId([
            'pensum_id' => $pensum->id,
            'profesor_id' => $profesorId,
            'lapso_id' => $lapsoId,
            'seccion_id' => $seccion->id,
            'objetivo' => 'Objetivo estructura N',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $finicial = now()->subDays(2)->toDateString();
        $creada = now()->subDay()->setTime(12, 0)->toDateTimeString();

        for ($i = 1; $i <= $total; $i++) {
            DB::table('activities')->insert([
                'pevaluacion_id' => $pevaluacionId,
                'finicial' => $finicial,
                'ffinal' => $finicial,
                'topic' => 'Tema estructura N-'.$i,
                'teaching' => $i === 1
                    ? 'Enseñanza planificada con actividades claras para todos los estudiantes del grupo durante toda la semana escolar completa'
                    : 'Texto corto '.$i,
                'description' => $i === 1 ? 'Evaluativo N' : null,
                'status' => $i === 1,
                'created_at' => $creada,
                'updated_at' => $creada,
            ]);
        }

        return [$user, now()->subDays(6)->toDateString(), now()->toDateString()];
    }
}
