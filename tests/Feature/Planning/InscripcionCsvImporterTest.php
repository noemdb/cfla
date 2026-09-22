<?php

namespace Tests\Feature\Planning;

use App\Models\app\Academy\Escolaridad;
use App\Models\app\Academy\Grado;
use App\Models\app\Academy\Inscripcion;
use App\Models\app\Academy\Pestudio;
use App\Models\app\Academy\Programacion;
use App\Models\app\Academy\Seccion;
use App\Models\app\Academy\Tinscripcion;
use App\Models\app\Learner\Estudiant;
use App\Models\app\Learner\Representant;
use App\Services\Planning\InscripcionCsvImporter;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class InscripcionCsvImporterTest extends TestCase
{
    use DatabaseTransactions;

    private InscripcionCsvImporter $importer;

    protected function setUp(): void
    {
        parent::setUp();

        $this->importer = app(InscripcionCsvImporter::class);
    }

    // ─── Helpers ───────────────────────────────────────────────

    private function writeCsv(string $content): string
    {
        $path = tempnam(sys_get_temp_dir(), 'csv_test_');
        file_put_contents($path, $content);

        return $path;
    }

    private function createMinimalEstudiant(array $extra = []): Estudiant
    {
        $representant = Representant::factory()->create();
        $planpagoId = DB::table('planpagos')->insertGetId([
            'name' => 'Plan de Prueba',
            'description' => 'Plan de pago de prueba',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return Estudiant::create(array_merge([
            'ci_estudiant' => fake()->unique()->numerify('########'),
            'name' => fake()->firstName(),
            'lastname' => fake()->lastName(),
            'representant_id' => $representant->id,
            'planpago_id' => $planpagoId,
            'type_ci_id' => 1,
            'gender' => 'Masculino',
            'date_birth' => '2010-01-15',
            'status_active' => 'true',
            'status_blacklist' => 'false',
        ], $extra));
    }

    /**
     * @return array{0: Pestudio, 1: Grado, 2: Seccion}
     */
    private function makeSection(string $gradoName, string $seccionName): array
    {
        $pestudio = Pestudio::factory()->create(['status_active' => 'true']);
        $grado = Grado::factory()->create([
            'pestudio_id' => $pestudio->id,
            'name' => $gradoName,
            'status_active' => 'true',
        ]);
        $seccion = Seccion::factory()->create([
            'grado_id' => $grado->id,
            'name' => $seccionName,
            'status_active' => 'true',
        ]);

        return [$pestudio, $grado, $seccion];
    }

    /** @return array<string, mixed> */
    private function baseImportOptions(array $extra = []): array
    {
        return array_merge([
            'tipo_id' => Tinscripcion::factory()->create()->id,
            'escolaridad_id' => Escolaridad::factory()->create()->id,
            'programacion_id' => Programacion::factory()->create()->id,
            'grupo_estable_id' => null,
            'plan_pago_id' => null,
            'representant_ci' => null,
        ], $extra);
    }

    // ─── Parseo ────────────────────────────────────────────────

    /** @test */
    public function it_parses_comma_with_header(): void
    {
        $path = $this->writeCsv("ci_estudiant,lastname,name,grado,seccion\n123,PER,ANA,GRADO,A\n");

        $rows = $this->importer->parse($path);

        $this->assertCount(1, $rows);
        $this->assertSame('123', $rows[0]['ci_estudiant']);
        $this->assertSame(2, $rows[0]['_line']);
    }

    /** @test */
    public function it_parses_without_header_using_positional_columns(): void
    {
        $path = $this->writeCsv("123,PER,ANA,GRADO,A\n");

        $rows = $this->importer->parse($path);

        $this->assertCount(1, $rows);
        $this->assertSame('PER', $rows[0]['lastname']);
        $this->assertSame('ANA', $rows[0]['name']);
    }

    /** @test */
    public function it_parses_semicolon_delimiter(): void
    {
        $path = $this->writeCsv("ci_estudiant;lastname;name;grado;seccion\n123;PER;ANA;GRADO;A\n");

        $rows = $this->importer->parse($path);

        $this->assertCount(1, $rows);
        $this->assertSame('PER', $rows[0]['lastname']);
        $this->assertSame('A', $rows[0]['seccion']);
    }

    /** @test */
    public function it_strips_utf8_bom(): void
    {
        $path = $this->writeCsv("\xEF\xBB\xBFci_estudiant,lastname,name,grado,seccion\n123,PER,ANA,GRADO,A\n");

        $rows = $this->importer->parse($path);

        $this->assertCount(1, $rows);
        $this->assertSame('123', $rows[0]['ci_estudiant']);
    }

    /** @test */
    public function it_converts_windows_1252_encoding(): void
    {
        $content = mb_convert_encoding(
            "ci_estudiant,lastname,name,grado,seccion\n123,MUÑOZ,JOSÉ,GRADO,A\n",
            'Windows-1252',
            'UTF-8'
        );
        $path = $this->writeCsv($content);

        $rows = $this->importer->parse($path);

        $this->assertSame('MUÑOZ', $rows[0]['lastname']);
        $this->assertSame('JOSÉ', $rows[0]['name']);
    }

    /** @test */
    public function it_converts_utf16_le_with_bom(): void
    {
        $content = "\xFF\xFE".mb_convert_encoding(
            "ci_estudiant,lastname,name,grado,seccion\n123,PER,ANA,GRADO,A\n",
            'UTF-16LE',
            'UTF-8'
        );
        $path = $this->writeCsv($content);

        $rows = $this->importer->parse($path);

        $this->assertCount(1, $rows);
        $this->assertSame('123', $rows[0]['ci_estudiant']);
        $this->assertSame('A', $rows[0]['seccion']);
    }

    /** @test */
    public function it_converts_utf16_be_with_bom(): void
    {
        $content = "\xFE\xFF".mb_convert_encoding(
            "ci_estudiant,lastname,name,grado,seccion\n123,PER,ANA,GRADO,A\n",
            'UTF-16BE',
            'UTF-8'
        );
        $path = $this->writeCsv($content);

        $rows = $this->importer->parse($path);

        $this->assertCount(1, $rows);
        $this->assertSame('123', $rows[0]['ci_estudiant']);
    }

    /** @test */
    public function it_maps_spanish_header(): void
    {
        $path = $this->writeCsv("cédula,apellido,nombre,grado,sección\n123,PER,ANA,GRADO,A\n");

        $rows = $this->importer->parse($path);

        $this->assertCount(1, $rows);
        $this->assertSame('PER', $rows[0]['lastname']);
        $this->assertSame('ANA', $rows[0]['name']);
    }

    // ─── Normalización ─────────────────────────────────────────

    /** @test */
    public function it_normalizes_ci_prefix_and_separators(): void
    {
        $this->assertSame('12345678', $this->importer->normalizeCi('V-12.345.678'));
        $this->assertSame('12345678', $this->importer->normalizeCi(' 12 345 678 '));
        $this->assertSame('12345678', $this->importer->normalizeCi('12345678'));
    }

    /** @test */
    public function it_resolves_section_ignoring_accents_case_and_spaces(): void
    {
        [, , $seccion] = $this->makeSection('GRADO ÑANDÚ', 'B');

        $resolved = $this->importer->resolveSeccion('grado   nandu', 'b');

        $this->assertNotNull($resolved);
        $this->assertSame($seccion->id, $resolved->id);
    }

    /** @test */
    public function it_returns_null_when_grado_is_ambiguous(): void
    {
        [, , $seccionA] = $this->makeSection('GRADO AMB', 'A');
        $this->makeSection('GRADO AMB', 'A');

        $this->assertNull($this->importer->resolveSeccion('GRADO AMB', 'A'));
        $this->assertNotNull($seccionA);
    }

    // ─── Vista previa ──────────────────────────────────────────

    /** @test */
    public function it_marks_missing_fields_as_error(): void
    {
        $rows = $this->importer->parse($this->writeCsv("ci,lastname,name,grado,seccion\n,X,Y,GRADO,A\n"));

        $preview = $this->importer->preview($rows);

        $this->assertSame('error', $preview[0]['status']);
    }

    /** @test */
    public function it_marks_invalid_section_as_error(): void
    {
        $this->makeSection('GRADO OK', 'A');
        $rows = $this->importer->parse($this->writeCsv("ci,lastname,name,grado,seccion\n55500010,X,Y,GRADO OK,Z\n"));

        $preview = $this->importer->preview($rows);

        $this->assertSame('error', $preview[0]['status']);
        $this->assertStringContainsString('No se encontró la sección', $preview[0]['message']);
    }

    /** @test */
    public function it_detects_duplicate_ci_within_the_file(): void
    {
        $this->makeSection('GRADO DUP', 'A');
        $csv = "ci,lastname,name,grado,seccion\n"
            ."55500009,X,Y,GRADO DUP,A\n"
            ."55500009,X,Y,GRADO DUP,A\n";

        $preview = $this->importer->preview($this->importer->parse($this->writeCsv($csv)));

        $this->assertFalse($preview[0]['duplicate']);
        $this->assertTrue($preview[1]['duplicate']);
        $this->assertSame('sin_cambios', $preview[1]['action']);
    }

    // ─── Importación ───────────────────────────────────────────

    /** @test */
    public function it_creates_student_and_inscription(): void
    {
        [, , $seccion] = $this->makeSection('GRADO NEW', 'A');
        $options = $this->baseImportOptions();
        $rows = $this->importer->parse($this->writeCsv("ci,lastname,name,grado,seccion\n55500013,CSV,NUEVO,GRADO NEW,A\n"));

        $preview = $this->importer->preview($rows, $options);
        $report = $this->importer->import($preview, $options);

        $this->assertSame(1, $report['created']);
        $this->assertSame(1, $report['inscribed']);

        $estudiant = Estudiant::where('ci_estudiant', '55500013')->first();
        $this->assertNotNull($estudiant);
        $this->assertDatabaseHas('inscripcions', [
            'estudiant_id' => $estudiant->id,
            'seccion_id' => $seccion->id,
        ]);
    }

    /** @test */
    public function it_updates_academic_data_when_option_enabled(): void
    {
        [, $grado, $seccionA] = $this->makeSection('GRADO AC', 'A');
        $seccionB = Seccion::factory()->create(['grado_id' => $grado->id, 'name' => 'B', 'status_active' => 'true']);

        $tipoOld = Tinscripcion::factory()->create();
        $tipoNew = Tinscripcion::factory()->create();
        $escolaridadOld = Escolaridad::factory()->create();
        $escolaridadNew = Escolaridad::factory()->create();
        $programacionOld = Programacion::factory()->create();
        $programacionNew = Programacion::factory()->create();

        $estudiant = $this->createMinimalEstudiant(['ci_estudiant' => '55500011']);
        $inscripcion = Inscripcion::factory()->create([
            'estudiant_id' => $estudiant->id,
            'seccion_id' => $seccionA->id,
            'tipo_id' => $tipoOld->id,
            'escolaridad_id' => $escolaridadOld->id,
            'programacion_id' => $programacionOld->id,
        ]);

        $options = [
            'tipo_id' => $tipoNew->id,
            'escolaridad_id' => $escolaridadNew->id,
            'programacion_id' => $programacionNew->id,
            'grupo_estable_id' => null,
            'plan_pago_id' => null,
            'representant_ci' => null,
            'update_academic_data' => true,
        ];

        $rows = $this->importer->parse($this->writeCsv("ci,lastname,name,grado,seccion\n55500011,X,Y,GRADO AC,B\n"));
        $preview = $this->importer->preview($rows, $options);

        $this->assertSame('actualizar', $preview[0]['action']);

        $report = $this->importer->import($preview, $options);

        $this->assertSame(1, $report['updated']);
        $this->assertDatabaseHas('inscripcions', [
            'id' => $inscripcion->id,
            'seccion_id' => $seccionB->id,
            'tipo_id' => $tipoNew->id,
            'escolaridad_id' => $escolaridadNew->id,
            'programacion_id' => $programacionNew->id,
        ]);
    }

    /** @test */
    public function it_does_not_touch_academic_data_when_option_disabled(): void
    {
        [, $grado, $seccionA] = $this->makeSection('GRADO NOAC', 'A');
        $seccionB = Seccion::factory()->create(['grado_id' => $grado->id, 'name' => 'B', 'status_active' => 'true']);

        $tipo = Tinscripcion::factory()->create();
        $escolaridad = Escolaridad::factory()->create();
        $programacion = Programacion::factory()->create();

        $estudiant = $this->createMinimalEstudiant(['ci_estudiant' => '55500014']);
        $inscripcion = Inscripcion::factory()->create([
            'estudiant_id' => $estudiant->id,
            'seccion_id' => $seccionA->id,
            'tipo_id' => $tipo->id,
            'escolaridad_id' => $escolaridad->id,
            'programacion_id' => $programacion->id,
        ]);

        $options = $this->baseImportOptions([
            'tipo_id' => Tinscripcion::factory()->create()->id,
            'escolaridad_id' => Escolaridad::factory()->create()->id,
            'programacion_id' => Programacion::factory()->create()->id,
        ]);

        $rows = $this->importer->parse($this->writeCsv("ci,lastname,name,grado,seccion\n55500014,X,Y,GRADO NOAC,B\n"));
        $this->importer->import($this->importer->preview($rows, $options), $options);

        $this->assertDatabaseHas('inscripcions', [
            'id' => $inscripcion->id,
            'seccion_id' => $seccionB->id,
            'tipo_id' => $tipo->id,
            'escolaridad_id' => $escolaridad->id,
            'programacion_id' => $programacion->id,
        ]);
    }

    /** @test */
    public function it_updates_student_names_when_option_enabled(): void
    {
        $this->makeSection('GRADO NM', 'A');
        $estudiant = $this->createMinimalEstudiant([
            'ci_estudiant' => '55500012',
            'name' => 'VIEJO',
            'lastname' => 'VIEJO',
        ]);

        $options = $this->baseImportOptions(['update_student_names' => true]);
        $rows = $this->importer->parse($this->writeCsv("ci,lastname,name,grado,seccion\n55500012,NUEVO,NOMBRE,GRADO NM,A\n"));

        $this->importer->import($this->importer->preview($rows, $options), $options);

        $this->assertSame('NOMBRE', $estudiant->fresh()->name);
        $this->assertSame('NUEVO', $estudiant->fresh()->lastname);
    }
}
