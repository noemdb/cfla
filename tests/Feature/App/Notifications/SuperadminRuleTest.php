<?php

namespace Tests\Feature\App\Notifications;

use App\Models\User;
use App\Notifications\ActivityCreatedNotification;
use App\Notifications\AdminLogErrorNotification;
use App\Services\NotificationService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

/**
 * Regla del superadmin (`config/notifications.php#superadmin_id`): solo
 * recibe los tipos listados en `superadmin_types` (alertas de error del log).
 * Todo lo demás se filtra aunque tenga otros roles (la audiencia de los
 * observers lo incluiría por is_admin).
 */
class SuperadminRuleTest extends TestCase
{
    use DatabaseTransactions;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        // El superadmin de la regla es un usuario de pruebas, no la fila real
        // (id=1): así no se toca la bandeja de nadie.
        $this->admin = User::factory()->create([
            'is_admin' => true,
            'is_planner' => true,
            'is_leadership' => true,
            'is_active' => 'enable',
        ]);

        config([
            'notifications.superadmin_id' => $this->admin->id,
            'notifications.superadmin_types' => ['laravel_log_error'],
        ]);
    }

    private function activityNotification(): ActivityCreatedNotification
    {
        return new ActivityCreatedNotification(
            type: 'activity_created',
            message: 'Se registró una nueva actividad',
            url: route('app.notifications.index'),
            activityId: 1,
            pevaluacionId: 2,
        );
    }

    private function errorNotification(): AdminLogErrorNotification
    {
        return new AdminLogErrorNotification(
            type: AdminLogErrorNotification::TYPE,
            message: '[ERROR] laravel.log — boom',
            url: route('admin.logs'),
            logFile: 'laravel.log',
            logDate: now()->toDateTimeString(),
            logEnv: 'testing',
            logContext: null,
            logHash: sha1('boom-'.uniqid()),
            action: 'registró un error',
        );
    }

    public function test_filtra_los_tipos_operativos_aunque_tenga_otros_roles(): void
    {
        $sent = app(NotificationService::class)->notifyUsers([$this->admin], $this->activityNotification());

        $this->assertSame(0, $sent);
        $this->assertSame(0, $this->admin->notifications()->count());
    }

    public function test_deja_pasar_las_alertas_de_error(): void
    {
        $sent = app(NotificationService::class)->notifyUsers([$this->admin], $this->errorNotification());

        $this->assertSame(1, $sent);

        $row = $this->admin->notifications()->latest('created_at')->first();
        $this->assertSame(AdminLogErrorNotification::class, $row->type);
        $this->assertSame('laravel_log_error', $row->data['type']);
        $this->assertSame(route('admin.logs'), $row->data['url']);
    }

    public function test_no_afecta_a_los_demas_usuarios(): void
    {
        $planner = User::factory()->create(['is_planner' => true, 'is_active' => 'enable']);

        $sent = app(NotificationService::class)->notifyUsers([$planner, $this->admin], $this->activityNotification());

        $this->assertSame(1, $sent);
        $this->assertSame(1, $planner->notifications()->count());
        $this->assertSame(0, $this->admin->notifications()->count());
    }

    public function test_la_alerta_de_error_tambien_llega_a_usuarios_normales(): void
    {
        $planner = User::factory()->create(['is_planner' => true, 'is_active' => 'enable']);

        $sent = app(NotificationService::class)->notifyUsers([$planner, $this->admin], $this->errorNotification());

        $this->assertSame(2, $sent);
    }

    public function test_is_superadmin_allowed_predice_el_filtrado(): void
    {
        $service = app(NotificationService::class);
        $planner = User::factory()->create(['is_planner' => true, 'is_active' => 'enable']);

        $this->assertTrue($service->isSuperadminAllowed($planner, $this->activityNotification()));
        $this->assertTrue($service->isSuperadminAllowed($planner, $this->errorNotification()));
        $this->assertTrue($service->isSuperadminAllowed($this->admin, $this->errorNotification()));
        $this->assertFalse($service->isSuperadminAllowed($this->admin, $this->activityNotification()));
    }
}
