<?php

namespace Database\Seeders;

use App\Models\app\Academy\Grado;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * Catálogo de áreas y expectativas de aprendizaje de Educación Inicial.
 *
 * 9 áreas × 5 expectativas = 45 por grupo de edad, replicadas en los 3 grupos
 * → 27 áreas y 135 expectativas.
 *
 * POR QUÉ ESTE SEEDER EXISTE
 * ──────────────────────────
 * Es el prerrequisito del subsistema de informes finales (`Eifinalk`), que en
 * la plataforma legacy NUNCA operó: `eilearningareas` y
 * `eilearningexpectations` quedaron en 0 filas porque su
 * `EILearningSeeder` jamás se ejecutó (el `DatabaseSeeder` del legacy solo
 * llamaba a `DiagnosticsSeeder`). Sin catálogo no hay informe final posible,
 * así que en cfla es greenfield y este seeder es su condición de arranque.
 *
 * Datos extraídos literalmente del legacy
 * (`saefl/s2526/database/seeds/EILearningSeeder.php`). Verificado: las
 * expectativas son idénticas en los tres grupos, por eso el catálogo se
 * declara UNA vez y se replica con un bucle en lugar de las 1.021 líneas
 * originales de `insert` repetidos.
 *
 * CORRECCIÓN RESPECTO AL LEGACY
 * ────────────────────────────
 * `eilearningareas.description` es `text NOT NULL` sin default y el seeder
 * legacy NO lo insertaba: con `sql_mode` estricto eso aborta con
 * "Field 'description' doesn't have a default value". Como ninguna vista lee
 * esa columna (la UI solo usa `name` y `expectation->description`), aquí se
 * rellena con el propio nombre del área: cumple el NOT NULL sin inventar
 * contenido pedagógico.
 *
 * Es IDEMPOTENTE: se puede reejecutar sin duplicar ni truncar nada (el
 * proyecto prohíbe TRUNCATE/DROP). Las áreas se buscan por
 * `grado_id` + `name` y las expectativas por `eilearningarea_id` +
 * `description`.
 */
class EILearningSeeder extends Seeder
{
    /**
     * Grupos de edad (grados) de Educación Inicial. Los mismos ids en `s2526`
     * y `s2627`: 22 = 1ER GRUPO, 23 = 2DO GRUPO, 24 = 3ER GRUPO.
     */
    const GRADOS_INICIAL = [22, 23, 24];

    /**
     * Catálogo curricular: 9 áreas con sus 5 expectativas de aprendizaje.
     */
    const AREAS = [
        [
            'name' => 'Formación personal, social y comunicación',
            'expectations' => [
                'Comparte objetos y alimentos con sus pares y adultos.',
                'Se integra progresivamente a juegos colectivos con respeto y afecto.',
                'Reconoce normas básicas de cortesía: saludar, agradecer, disculparse.',
                'Expresa emociones mediante gestos, palabras o juegos simbólicos.',
                'Participa en celebraciones y actividades comunitarias escolares.',
            ],
        ],
        [
            'name' => 'Relación con el ambiente',
            'expectations' => [
                'Explora el entorno inmediato identificando elementos naturales.',
                'Participa en el cuidado de plantas y animales.',
                'Reconoce cambios en el clima y estaciones del año.',
                'Distingue entre objetos naturales y artificiales.',
                'Manifiesta interés por fenómenos de la naturaleza.',
            ],
        ],
        [
            'name' => 'Lenguaje oral y escrito',
            'expectations' => [
                'Se comunica usando frases sencillas para expresar necesidades.',
                'Reconoce imágenes y símbolos en cuentos o textos ilustrados.',
                'Anticipa contenido de cuentos por ilustraciones.',
                'Participa en juegos de rimas y canciones.',
                'Inventa cuentos o anécdotas simples a partir de experiencias.',
            ],
        ],
        [
            'name' => 'Procesos lógico-matemáticos',
            'expectations' => [
                'Clasifica objetos por color, forma o tamaño.',
                'Cuenta objetos hasta cinco o más.',
                'Identifica relaciones espaciales básicas (arriba, abajo, cerca...).',
                'Agrupa elementos según cantidad o similitud.',
                'Reconoce figuras geométricas básicas: círculo, cuadrado, triángulo.',
            ],
        ],
        [
            'name' => 'Expresión plástica, corporal y musical',
            'expectations' => [
                'Utiliza materiales como plastilina, témperas o papel para crear.',
                'Baila y canta al ritmo de canciones infantiles.',
                'Representa personas u objetos mediante dibujos simples.',
                'Coordina movimientos con música o instrumentos.',
                'Explora sonidos del cuerpo y objetos para crear ritmos.',
            ],
        ],
        [
            'name' => 'Imitación y juegos de roles',
            'expectations' => [
                'Imita acciones de personas conocidas (familiares, docentes).',
                'Participa en dramatizaciones de cuentos.',
                'Crea personajes e historias en juegos simbólicos.',
                'Utiliza disfraces y materiales para representar roles sociales.',
                'Asume roles de grupo en situaciones de juego organizado.',
            ],
        ],
        [
            'name' => 'Educación vial y ciudadana',
            'expectations' => [
                'Reconoce señales de tránsito básicas (alto, paso peatonal...).',
                'Aplica normas de seguridad al transitar por la calle.',
                'Participa en simulacros de emergencia escolar.',
                'Conoce los colores del semáforo y su significado.',
                'Demuestra respeto por normas de convivencia escolar.',
            ],
        ],
        [
            'name' => 'Salud integral y hábitos',
            'expectations' => [
                'Practica hábitos de higiene personal diariamente.',
                'Identifica alimentos saludables y no saludables.',
                'Participa en rutinas de alimentación, descanso y aseo.',
                'Reconoce señales de malestar y pide ayuda.',
                'Colabora con la limpieza y orden del aula.',
            ],
        ],
        [
            'name' => 'Identidad, historia y cultura',
            'expectations' => [
                'Dice su nombre, apellido y el de familiares cercanos.',
                'Identifica símbolos patrios como bandera, himno y escudo.',
                'Participa en celebraciones patrias o tradicionales.',
                'Reconoce personajes históricos locales en cuentos o murales.',
                'Manifiesta orgullo por su comunidad y cultura.',
            ],
        ],
    ];

    public function run(): void
    {
        $this->verificarGrados();

        $now = Carbon::now();
        $areasCreadas = 0;
        $areasExistentes = 0;
        $expectativasCreadas = 0;
        $expectativasExistentes = 0;

        DB::transaction(function () use ($now, &$areasCreadas, &$areasExistentes, &$expectativasCreadas, &$expectativasExistentes) {
            foreach (self::GRADOS_INICIAL as $gradoId) {
                foreach (self::AREAS as $area) {
                    $areaId = DB::table('eilearningareas')
                        ->where('grado_id', $gradoId)
                        ->where('name', $area['name'])
                        ->value('id');

                    if (! $areaId) {
                        $areaId = DB::table('eilearningareas')->insertGetId([
                            'grado_id' => $gradoId,
                            'name' => $area['name'],
                            // Ver "CORRECCIÓN RESPECTO AL LEGACY" en el docblock.
                            'description' => $area['name'],
                            'created_at' => $now,
                            'updated_at' => $now,
                        ]);
                        $areasCreadas++;
                    } else {
                        $areasExistentes++;
                    }

                    foreach ($area['expectations'] as $descripcion) {
                        $existe = DB::table('eilearningexpectations')
                            ->where('eilearningarea_id', $areaId)
                            ->where('description', $descripcion)
                            ->exists();

                        if ($existe) {
                            $expectativasExistentes++;

                            continue;
                        }

                        DB::table('eilearningexpectations')->insert([
                            'eilearningarea_id' => $areaId,
                            'description' => $descripcion,
                            'created_at' => $now,
                            'updated_at' => $now,
                        ]);
                        $expectativasCreadas++;
                    }
                }
            }
        });

        $this->command?->info(sprintf(
            'EILearningSeeder: %d áreas creadas, %d ya existentes · %d expectativas creadas, %d ya existentes.',
            $areasCreadas,
            $areasExistentes,
            $expectativasCreadas,
            $expectativasExistentes
        ));

        $this->command?->warn(
            'Áreas por grado (solo con expectativas): '.$this->resumenPorGrado().' (esperado 9 en cada uno).'
        );
    }

    /**
     * Corta en seco si falta algún grado de Educación Inicial: sembrar
     * `eilearningareas` con un `grado_id` inexistente dejaría áreas
     * huérfanas que ni el informe final ni la UI podrían alcanzar.
     */
    private function verificarGrados(): void
    {
        $faltantes = array_diff(self::GRADOS_INICIAL, Grado::query()->pluck('id')->all());

        if ($faltantes) {
            throw new \RuntimeException(
                'EILearningSeeder abortado: no existen los grados de Educación Inicial '
                .implode(', ', $faltantes).'. Revisa `grados` antes de sembrar.'
            );
        }
    }

    /**
     * @return string "1ER GRUPO=9, 2DO GRUPO=9, 3ER GRUPO=9"
     */
    private function resumenPorGrado(): string
    {
        $conteo = DB::table('eilearningareas')
            ->select('grado_id', DB::raw('count(*) as total'))
            ->whereIn('grado_id', self::GRADOS_INICIAL)
            ->groupBy('grado_id')
            ->pluck('total', 'grado_id');

        $nombres = Grado::query()
            ->whereIn('id', self::GRADOS_INICIAL)
            ->pluck('name', 'id');

        $partes = [];
        foreach (self::GRADOS_INICIAL as $gradoId) {
            $partes[] = sprintf(
                '%s=%s',
                $nombres[$gradoId] ?? ('grado '.$gradoId),
                $conteo[$gradoId] ?? 0
            );
        }

        return implode(', ', $partes);
    }
}
