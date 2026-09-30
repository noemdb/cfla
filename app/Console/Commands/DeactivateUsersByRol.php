<?php

namespace App\Console\Commands;

use App\Models\sys\Rol;
use App\Models\User;
use Illuminate\Console\Command;

class DeactivateUsersByRol extends Command
{
    /**
     * Uso:
     * php8.2 artisan user:desactiveRol --rol="ESTUDIANTE"
     * php8.2 artisan user:desactiveRol --rol="REPRESENTANTE"
     *
     * @var string
     */
    protected $signature = 'user:desactiveRol
        {--rol= : Valor de rols.rol a desactivar (ej. ESTUDIANTE, PROFESOR, REPRESENTANTE)}
        {--force : Omite la confirmación interactiva}
        {--dry-run : Solo muestra cuántos usuarios serían desactivados, sin modificar nada}';

    protected $description = 'Desactiva (users.is_active="disable") todos los usuarios vinculados a un valor de rols.rol';

    public function handle(): int
    {
        $rol = trim((string) ($this->option('rol') ?? ''));

        if ($rol === '') {
            $this->error('Debe indicar el rol: php artisan user:desactiveRol --rol="ESTUDIANTE"');
            $this->availableRols();

            return Command::FAILURE;
        }

        $rol = strtoupper($rol);

        // Valida que el rol exista en la tabla rols (ignora soft-deleted, igual que el modelo).
        if (! Rol::where('rol', $rol)->exists()) {
            $this->error("No existe ningún registro en rols con rol=\"{$rol}\". No se modificó nada.");
            $this->availableRols();

            return Command::FAILURE;
        }

        $userIds = Rol::where('rol', $rol)->distinct()->pluck('user_id')->unique()->values();

        if ($userIds->isEmpty()) {
            $this->warn("Rol \"{$rol}\" existe pero no tiene usuarios vinculados. Nada que hacer.");

            return Command::SUCCESS;
        }

        $total = $userIds->count();
        $alreadyDisabled = User::whereIn('id', $userIds)->where('is_active', 'disable')->count();
        $toDisable = $total - $alreadyDisabled;

        // Salvaguarda: avisar si el lote incluye administradores.
        $adminsAffected = User::whereIn('id', $userIds)
            ->where('is_admin', true)
            ->where('is_active', 'enable')
            ->count();

        $this->table(
            ['Métrica', 'Cantidad'],
            [
                ['Rol (rols.rol)', $rol],
                ['Usuarios vinculados', $total],
                ['Ya desactivados', $alreadyDisabled],
                ['Por desactivar', $toDisable],
                ['Administradores activos en el lote', $adminsAffected],
            ]
        );

        if ($toDisable === 0) {
            $this->info('Todos los usuarios vinculados ya están en is_active="disable". Nada que hacer.');

            return Command::SUCCESS;
        }

        if ($this->option('dry-run')) {
            $this->info("[dry-run] No se modificó nada. {$toDisable} usuario(s) serían desactivados.");

            return Command::SUCCESS;
        }

        if ($adminsAffected > 0) {
            $this->warn("¡Atención! El lote incluye {$adminsAffected} administrador(es) activo(s). Desactivarlos puede dejar el sistema sin acceso admin.");
        }

        if (! $this->option('force') && ! $this->confirm("¿Desactivar {$toDisable} usuario(s) con rol \"{$rol}\" (is_active=\"disable\")?", false)) {
            $this->info('Operación cancelada. No se modificó nada.');

            return Command::SUCCESS;
        }

        $updated = User::whereIn('id', $userIds)
            ->where('is_active', '!=', 'disable')
            ->update(['is_active' => 'disable']);

        $this->info("Listo: {$updated} usuario(s) con rol \"{$rol}\" pasaron a is_active=\"disable\".");

        return Command::SUCCESS;
    }

    private function availableRols(): void
    {
        $rols = Rol::select('rol')
            ->selectRaw('COUNT(*) as total')
            ->groupBy('rol')
            ->orderBy('rol')
            ->get(['rol', 'total']);

        if ($rols->isEmpty()) {
            return;
        }

        $this->line('Roles presentes en rols:');
        $this->table(
            ['rols.rol', 'Registros'],
            $rols->map(fn ($r) => [$r->rol, (int) $r->total])->toArray()
        );
    }
}
