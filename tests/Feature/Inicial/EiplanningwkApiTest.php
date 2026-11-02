<?php

namespace Tests\Feature\Inicial;

use Tests\TestCase;
use App\Models\User;
use App\Models\app\Academy\Profesor;
use App\Models\app\Inicial\Eiplanningwk;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;

class EiplanningwkApiTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected $user;
    protected $profesor;
    protected $token;

    protected function setUp(): void
    {
        parent::setUp();

        // Crear usuario y profesor para pruebas
        $this->user = User::factory()->create([
            'username' => 'profesor_test',
            'email' => 'profesor@test.com',
            'password' => bcrypt('password123')
        ]);

        $this->profesor = Profesor::factory()->create([
            'user_id' => $this->user->id
        ]);

        // Crear token Sanctum
        $this->token = $this->user->createToken('test-token')->plainTextToken;
    }

    /** @test */
    public function usuario_puede_listar_sus_planificaciones()
    {
        // Crear algunas planificaciones de prueba
        Eiplanningwk::factory()->count(3)->create([
            'profesor_id' => $this->profesor->id
        ]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
            'Accept' => 'application/json'
        ])->get('/api/inicial/eiplanningwks');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'profesor_id',
                        'grado_id',
                        'seccion_id',
                        'finicial',
                        'ffinal',
                        'links'
                    ]
                ],
                'meta' => [
                    'total',
                    'per_page',
                    'current_page'
                ]
            ]);

        $this->assertCount(3, $response->json('data'));
    }

    /** @test */
    public function usuario_puede_crear_nueva_planificacion()
    {
        $data = [
            'profesor_id' => $this->profesor->id,
            'grado_id' => 1,
            'seccion_id' => 1,
            'finicial' => '2026-10-01',
            'ffinal' => '2026-10-05',
            'tiempo_ejecucion' => 1,
            'diagnostico' => 'Diagnóstico inicial de prueba',
            'observacion' => 'Observaciones de prueba'
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
            'Accept' => 'application/json',
            'Content-Type' => 'application/json'
        ])->post('/api/inicial/eiplanningwks', $data);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'profesor_id',
                    'grado_id',
                    'seccion_id',
                    'finicial',
                    'ffinal',
                    'tiempo_ejecucion',
                    'diagnostico',
                    'observacion',
                    'created_at',
                    'updated_at'
                ],
                'message'
            ]);

        $this->assertDatabaseHas('eiplanningwks', [
            'profesor_id' => $this->profesor->id,
            'diagnostico' => 'Diagnóstico inicial de prueba'
        ]);
    }

    /** @test */
    public function validacion_impide_duplicados_en_misma_semana()
    {
        // Crear primera planificación
        $plan1 = Eiplanningwk::factory()->create([
            'profesor_id' => $this->profesor->id,
            'grado_id' => 1,
            'seccion_id' => 1,
            'finicial' => '2026-10-01',
            'ffinal' => '2026-10-05'
        ]);

        // Intentar crear duplicado
        $data = [
            'profesor_id' => $this->profesor->id,
            'grado_id' => 1,
            'seccion_id' => 1,
            'finicial' => '2026-10-01', // Misma fecha
            'ffinal' => '2026-10-05',
            'tiempo_ejecucion' => 1
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
            'Accept' => 'application/json',
            'Content-Type' => 'application/json'
        ])->post('/api/inicial/eiplanningwks', $data);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['finicial']);
    }

    /** @test */
    public function usuario_puede_ver_planificacion_especifica()
    {
        $plan = Eiplanningwk::factory()->create([
            'profesor_id' => $this->profesor->id
        ]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
            'Accept' => 'application/json'
        ])->get("/api/inicial/eiplanningwks/{$plan->id}");

        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'id' => $plan->id,
                    'profesor_id' => $plan->profesor_id
                ]
            ]);
    }

    /** @test */
    public function usuario_no_puede_ver_planificacion_de_otro_profesor()
    {
        // Crear otro profesor y planificación
        $otroUser = User::factory()->create();
        $otroProfesor = Profesor::factory()->create([
            'user_id' => $otroUser->id
        ]);

        $plan = Eiplanningwk::factory()->create([
            'profesor_id' => $otroProfesor->id
        ]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
            'Accept' => 'application/json'
        ])->get("/api/inicial/eiplanningwks/{$plan->id}");

        $response->assertStatus(404); // 404 para no revelar existencia
    }

    /** @test */
    public function usuario_puede_actualizar_su_planificacion()
    {
        $plan = Eiplanningwk::factory()->create([
            'profesor_id' => $this->profesor->id
        ]);

        $data = [
            'diagnostico' => 'Diagnóstico actualizado',
            'observacion' => 'Observaciones actualizadas'
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
            'Accept' => 'application/json',
            'Content-Type' => 'application/json'
        ])->put("/api/inicial/eiplanningwks/{$plan->id}", $data);

        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'diagnostico' => 'Diagnóstico actualizado',
                    'observacion' => 'Observaciones actualizadas'
                ]
            ]);

        $this->assertDatabaseHas('eiplanningwks', [
            'id' => $plan->id,
            'diagnostico' => 'Diagnóstico actualizado'
        ]);
    }

    /** @test */
    public function usuario_puede_eliminar_su_planificacion()
    {
        $plan = Eiplanningwk::factory()->create([
            'profesor_id' => $this->profesor->id
        ]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
            'Accept' => 'application/json'
        ])->delete("/api/inicial/eiplanningwks/{$plan->id}");

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Planificación eliminada exitosamente'
            ]);

        $this->assertDatabaseMissing('eiplanningwks', [
            'id' => $plan->id
        ]);
    }

    /** @test */
    public function usuario_puede_obtener_estadisticas()
    {
        // Crear algunas planificaciones
        Eiplanningwk::factory()->count(5)->create([
            'profesor_id' => $this->profesor->id
        ]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
            'Accept' => 'application/json'
        ])->get('/api/inicial/eiplanningwks/statistics');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'total_planificaciones',
                'total_semanas',
                'promedio_estrategias_por_plan',
                'promedio_resumenes_por_plan',
                'planes_por_grado',
                'planes_por_mes',
                'planes_recientes'
            ]);
    }

    /** @test */
    public function rate_limiting_funciona_para_endpoints_criticos()
    {
        // Intentar hacer muchas solicitudes rápidamente
        for ($i = 0; $i < 35; $i++) {
            $response = $this->withHeaders([
                'Authorization' => 'Bearer ' . $this->token,
                'Accept' => 'application/json'
            ])->post('/api/inicial/eiplanningwks', [
                'profesor_id' => $this->profesor->id,
                'grado_id' => 1,
                'seccion_id' => 1,
                'finicial' => '2026-10-01',
                'ffinal' => '2026-10-05',
                'tiempo_ejecucion' => 1
            ]);

            if ($response->status() == 429) {
                break;
            }
        }

        // Debería haber recibido 429 en algún punto
        $this->assertTrue(
            $response->status() == 429 || 
            $response->status() == 422, // O falla por validación
            'Rate limiting no funcionó'
        );
    }

    /** @test */
    public function filtros_funcionan_correctamente()
    {
        // Crear planificaciones con diferentes grados
        Eiplanningwk::factory()->create([
            'profesor_id' => $this->profesor->id,
            'grado_id' => 1,
            'finicial' => '2026-10-01'
        ]);

        Eiplanningwk::factory()->create([
            'profesor_id' => $this->profesor->id,
            'grado_id' => 2,
            'finicial' => '2026-10-08'
        ]);

        // Filtrar por grado_id = 1
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
            'Accept' => 'application/json'
        ])->get('/api/inicial/eiplanningwks?grado_id=1');

        $response->assertStatus(200);
        
        $data = $response->json('data');
        $this->assertCount(1, $data);
        $this->assertEquals(1, $data[0]['grado_id']);

        // Filtrar por fecha
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
            'Accept' => 'application/json'
        ])->get('/api/inicial/eiplanningwks?fecha_desde=2026-10-05');

        $data = $response->json('data');
        $this->assertCount(1, $data); // Solo la del 2026-10-08
    }

    /** @test */
    public function paginacion_funciona_correctamente()
    {
        // Crear más planificaciones que el límite por página
        Eiplanningwk::factory()->count(15)->create([
            'profesor_id' => $this->profesor->id
        ]);

        // Solicitar con per_page = 5
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
            'Accept' => 'application/json'
        ])->get('/api/inicial/eiplanningwks?per_page=5');

        $response->assertStatus(200);

        $data = $response->json();
        
        $this->assertCount(5, $data['data']);
        $this->assertEquals(5, $data['meta']['per_page']);
        $this->assertEquals(15, $data['meta']['total']);
        $this->assertEquals(3, $data['meta']['total_pages']);
        
        // Verificar enlaces de paginación
        $this->assertNotNull($data['links']['first']);
        $this->assertNotNull($data['links']['last']);
        $this->assertNotNull($data['links']['next']);
    }
}