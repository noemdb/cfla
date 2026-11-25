<?php

namespace Tests\Feature\Console;

use App\Services\OpenRouterService;
use Illuminate\Support\Facades\DB;
use Mockery;
use Tests\TestCase;

class DbCorrectSpellingCommandTest extends TestCase
{
    private const TEST_CONNECTION = 'sqlite_spelling_test';

    protected function setUp(): void
    {
        parent::setUp();

        config()->set('database.connections.'.self::TEST_CONNECTION, [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
        ]);

        config()->set('openrouter.api_key', 'test-api-key');
        config()->set('openrouter.model_text_primary', 'test/model-primary');

        DB::connection(self::TEST_CONNECTION)->statement(
            'CREATE TABLE sample_subjects (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                name VARCHAR(50) NOT NULL,
                observations TEXT NULL,
                hours INTEGER DEFAULT 0,
                deleted_at TIMESTAMP NULL
            )'
        );
    }

    protected function tearDown(): void
    {
        DB::purge(self::TEST_CONNECTION);
        parent::tearDown();
    }

    public function test_fails_when_openrouter_api_key_is_missing(): void
    {
        config()->set('openrouter.api_key', null);

        $this->artisan('db:correct-spelling', [
            'table' => 'sample_subjects',
            'columns' => 'name',
            '--connection' => self::TEST_CONNECTION,
        ])->assertFailed();
    }

    public function test_fails_when_table_does_not_exist(): void
    {
        $this->artisan('db:correct-spelling', [
            'table' => 'non_existent_table',
            'columns' => 'name',
            '--connection' => self::TEST_CONNECTION,
        ])->assertFailed();
    }

    public function test_fails_when_column_is_not_text_or_is_primary_key(): void
    {
        $this->artisan('db:correct-spelling', [
            'table' => 'sample_subjects',
            'columns' => 'hours,id',
            '--connection' => self::TEST_CONNECTION,
        ])
            ->expectsOutputToContain("La columna 'hours' tiene tipo")
            ->expectsOutputToContain("La columna 'id' es clave primaria")
            ->assertFailed();
    }

    public function test_dry_run_reports_corrections_without_modifying_database(): void
    {
        DB::connection(self::TEST_CONNECTION)->table('sample_subjects')->insert([
            ['name' => 'EDUCACION FISICA', 'observations' => 'FISICA 1ER AÑO', 'hours' => 2],
        ]);

        $mock = Mockery::mock(OpenRouterService::class);
        $mock->shouldReceive('ask')
            ->twice()
            ->andReturn(
                [
                    'success' => true,
                    'content' => json_encode(['0' => 'EDUCACIÓN FÍSICA'], JSON_UNESCAPED_UNICODE),
                    'model' => 'test/model-primary',
                    'usage' => null,
                    'error' => null,
                ],
                [
                    'success' => true,
                    'content' => json_encode(['0' => 'FÍSICA 1ER AÑO'], JSON_UNESCAPED_UNICODE),
                    'model' => 'test/model-primary',
                    'usage' => null,
                    'error' => null,
                ]
            );

        $this->app->instance(OpenRouterService::class, $mock);

        $this->artisan('db:correct-spelling', [
            'table' => 'sample_subjects',
            'columns' => 'name,observations',
            '--connection' => self::TEST_CONNECTION,
            '--dry-run' => true,
        ])
            ->expectsOutputToContain('EDUCACIÓN FÍSICA')
            ->expectsOutputToContain('FÍSICA 1ER AÑO')
            ->assertSuccessful();

        $row = DB::connection(self::TEST_CONNECTION)->table('sample_subjects')->first();
        $this->assertSame('EDUCACION FISICA', $row->name);
        $this->assertSame('FISICA 1ER AÑO', $row->observations);
    }

    public function test_force_applies_corrections_and_skips_values_exceeding_max_length(): void
    {
        DB::connection(self::TEST_CONNECTION)->table('sample_subjects')->insert([
            ['name' => 'MATEMATICAS', 'observations' => 'QUIMICA 1ER AÑO', 'hours' => 3, 'deleted_at' => null],
            ['name' => 'ROBOTICA', 'observations' => null, 'hours' => 2, 'deleted_at' => null],
            ['name' => 'BIOLOGIA', 'observations' => null, 'hours' => 2, 'deleted_at' => now()],
        ]);

        $mock = Mockery::mock(OpenRouterService::class);
        $mock->shouldReceive('ask')
            ->twice()
            ->andReturn(
                [
                    'success' => true,
                    'content' => json_encode([
                        '0' => 'MATEMÁTICAS',
                        '1' => str_repeat('Á', 60), // excede varchar(50) -> debe omitirse
                    ], JSON_UNESCAPED_UNICODE),
                    'model' => 'test/model-primary',
                    'usage' => null,
                    'error' => null,
                ],
                [
                    'success' => true,
                    'content' => json_encode([
                        '0' => 'QUÍMICA 1ER AÑO',
                    ], JSON_UNESCAPED_UNICODE),
                    'model' => 'test/model-primary',
                    'usage' => null,
                    'error' => null,
                ]
            );

        $this->app->instance(OpenRouterService::class, $mock);

        $this->artisan('db:correct-spelling', [
            'table' => 'sample_subjects',
            'columns' => 'name,observations',
            '--connection' => self::TEST_CONNECTION,
            '--force' => true,
        ])
            ->expectsOutputToContain('supera el límite de 50 caracteres')
            ->assertSuccessful();

        $rows = DB::connection(self::TEST_CONNECTION)->table('sample_subjects')->orderBy('id')->get();
        $this->assertSame('MATEMÁTICAS', $rows[0]->name);
        $this->assertSame('QUÍMICA 1ER AÑO', $rows[0]->observations);
        $this->assertSame('ROBOTICA', $rows[1]->name); // omitido por exceder 50 chars
        $this->assertSame('BIOLOGIA', $rows[2]->name); // soft-deleted excluido por defecto
    }
}
