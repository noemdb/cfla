<?php

namespace Tests\Feature\Director;

use App\Services\Director\DirectorScopeService;
use Illuminate\Routing\Route;
use Illuminate\Support\Facades\Route as RouteFacade;
use ReflectionClass;
use ReflectionMethod;
use Tests\TestCase;

class DirectorReadOnlyTest extends TestCase
{
    /**
     * El rol is_director es 100% de solo lectura: el servicio de scope
     * no debe exponer ningún método que mute modelos (save/update/store/...).
     */
    public function test_service_does_not_expose_write_methods(): void
    {
        $publicMethods = array_map(
            fn ($m) => $m->name,
            (new ReflectionClass(DirectorScopeService::class))->getMethods(ReflectionMethod::IS_PUBLIC)
        );

        $forbidden = ['save', 'update', 'store', 'create', 'delete', 'destroy',
                      'approve', 'reject', 'comment', 'observe', 'saveObservation'];

        foreach ($forbidden as $word) {
            $hits = array_filter($publicMethods, fn ($m) => str_contains(strtolower($m), $word));

            $this->assertEmpty(
                $hits,
                "DirectorScopeService no debe exponer '{$word}'."
            );
        }
    }

    /**
     * Todas las rutas /app/director/* son de solo lectura (GET).
     * Refleja las rutas registradas: ninguna puede aceptar POST/PUT/PATCH/DELETE.
     */
    public function test_all_director_routes_are_get_only(): void
    {
        $directorRoutes = collect(RouteFacade::getRoutes())
            ->reject(fn (Route $r) => ! str_starts_with((string) $r->uri, 'app/director'));

        $this->assertNotEmpty($directorRoutes, 'No se encontraron rutas bajo app/director.');

        foreach ($directorRoutes as $route) {
            $this->assertNotContains('POST', $route->methods(), 'Ruta no-GET no permitida');
            $this->assertNotContains('PUT', $route->methods());
            $this->assertNotContains('PATCH', $route->methods());
            $this->assertNotContains('DELETE', $route->methods());
        }
    }

    /**
     * La vista de actividades del director es de SOLO VISUALIZACIÓN:
     * no debe contener <form> ni wire:submit (envíos de escritura) ni
     * wire:click que dispare métodos de mutación del componente.
     */
    public function test_activity_list_view_has_no_write_controls(): void
    {
        $source = file_get_contents(
            resource_path('views/livewire/director/activity-list.blade.php')
        );

        $this->assertStringNotContainsString('<form', $source);
        $this->assertStringNotContainsString('</form>', $source);
        $this->assertStringNotContainsString('wire:submit', $source);
        $this->assertStringNotContainsString('wire:click', $source);
        $this->assertStringNotContainsString('method="post"', $source);
        $this->assertStringNotContainsString('@csrf', $source);
    }
}
