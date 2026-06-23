<?php
namespace App\Console\Commands;

use App\Models\app\Enrollment\Catchment;
use App\Models\app\Enrollment\CatchmentInterview;
use App\Models\app\Institucion;
use App\Models\app\Institucion\Autoridad;
use App\Services\ResendEmailService;
use App\Services\SendPulseService;
use Illuminate\Console\Command;
use Jenssegers\Date\Date;

class TestInterviewEmailCommand extends Command
{
    /*
     * ══════════════════════════════════════════════════════════════════
     *  MODOS DISPONIBLES
     * ══════════════════════════════════════════════════════════════════
     *
     *  ── PRUEBA (un único registro, email de prueba) ──────────────────
     *  php artisan interview:test-email 10 noemdb@gmail.com --type=acceptance
     *  php artisan interview:test-email 10 noemdb@gmail.com --type=standby
     *  php artisan interview:test-email 10 noemdb@gmail.com --type=all
     *
     *  ── PRODUCCIÓN: envío masivo a emails originales ─────────────────
     *  php artisan interview:test-email 0 - --type=send-accepted
     *      → Envía carta de aceptación a TODOS los aceptados (notificados o no)
     *        y actualiza status_notified = true por cada envío exitoso.
     *
     *  php artisan interview:test-email 0 - --type=send-standby
     *      → Envía carta de lista en espera a TODOS los en-espera (notificados o no)
     *        y actualiza status_notified = true por cada envío exitoso.
     *
     *  ── Cambiar proveedor ────────────────────────────────────────────
     *  php artisan interview:test-email 10 noemdb@gmail.com --type=all --provider=resend
     */

    /** @var string */
    protected $signature = 'interview:test-email
                            {interview_id : ID de la entrevista (usa 0 en modos send-accepted / send-standby)}
                            {test_email   : Email de prueba; usa - en modos send-accepted / send-standby}
                            {--type=acceptance   : acceptance | standby | all | send-accepted | send-standby}
                            {--provider=sendpulse : Proveedor de email: sendpulse | resend}
                            {--status_copy       : Incluir copias CC y BCC (MAIL_CC_ADDRESS_CONTROL, etc)}';

    /** @var string */
    protected $description = 'Prueba / envía masivamente cartas de aceptación y lista en espera de matrícula';

    protected $emailService;

    /** Shared data cargada una sola vez para el batch */
    protected $institucion;
    protected $autoridad;
    protected $director;

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $interviewId = $this->argument('interview_id');
        $testEmail   = $this->argument('test_email');
        $type        = $this->option('type');
        $provider    = $this->option('provider');

        $validTypes = ['acceptance', 'standby', 'all', 'send-accepted', 'send-standby'];

        if (! in_array($type, $validTypes)) {
            $this->error("Tipo inválido: {$type}. Opciones: " . implode(' | ', $validTypes));
            return 1;
        }

        // ── Inicializar servicio ───────────────────────────────────────
        try {
            $this->emailService = $this->resolveEmailService($provider);
        } catch (\Exception $e) {
            $this->error("Error al inicializar el servicio de email: " . $e->getMessage());
            return 1;
        }

        // ── Modos de envío masivo ──────────────────────────────────────
        if ($type === 'send-accepted') {
            return $this->handleBulkAccepted($provider);
        }

        if ($type === 'send-standby') {
            return $this->handleBulkStandby($provider);
        }

        // ── Modo prueba (email de prueba) ──────────────────────────────
        return $this->handleTest($interviewId, $testEmail, $type, $provider);
    }

    // ══════════════════════════════════════════════════════════════════
    //  MODO PRUEBA
    // ══════════════════════════════════════════════════════════════════

    protected function handleTest(int $interviewId, string $testEmail, string $type, string $provider): int
    {
        if (! filter_var($testEmail, FILTER_VALIDATE_EMAIL)) {
            $this->error("El email de prueba no es válido: {$testEmail}");
            return 1;
        }

        $interview = CatchmentInterview::find($interviewId);

        if (! $interview) {
            $this->error("No se encontró ninguna entrevista con ID: {$interviewId}");
            return 1;
        }

        $this->info("─────────────────────────────────────────────────");
        $this->info("Entrevista encontrada:");
        $this->line("  ID              : {$interview->id}");
        $this->line("  Representante   : {$interview->full_name}");
        $this->line("  Estudiante      : {$interview->student_full_name}");
        $this->line("  Email original  : {$interview->email}");
        $this->line("  Aceptado        : " . ($interview->accepted ? 'Sí' : 'No'));
        $this->line("  En espera       : " . ($interview->status_standby ? 'Sí' : 'No'));
        $this->line("  Proveedor       : {$provider}");
        $this->line("  Copia CC/BCC    : " . ($this->option('status_copy') ? 'SÍ' : 'No'));
        $this->info("─────────────────────────────────────────────────");
        $this->warn("⚠  Los correos se enviarán a: {$testEmail} (no al email original)");
        $this->info("─────────────────────────────────────────────────");

        $errors = 0;

        if (in_array($type, ['acceptance', 'all'])) {
            $this->info("📧 Enviando carta de ACEPTACIÓN...");
            if ($this->sendAcceptanceEmail($interview, $testEmail)) {
                $this->info("   ✅ Carta de aceptación enviada correctamente a {$testEmail}");
            } else {
                $this->error("   ❌ Error al enviar carta de aceptación.");
                $errors++;
            }
        }

        if (in_array($type, ['standby', 'all'])) {
            $this->info("📧 Enviando carta de LISTA EN ESPERA...");
            if ($this->sendStandbyEmail($interview, $testEmail)) {
                $this->info("   ✅ Carta de lista en espera enviada correctamente a {$testEmail}");
            } else {
                $this->error("   ❌ Error al enviar carta de lista en espera.");
                $errors++;
            }
        }

        $this->info("─────────────────────────────────────────────────");

        if ($errors === 0) {
            $this->info("✅ Proceso completado sin errores.");
            return 0;
        }

        $this->error("⚠ Proceso completado con {$errors} error(es).");
        return 1;
    }

    // ══════════════════════════════════════════════════════════════════
    //  MODO MASIVO: CARTAS DE ACEPTACIÓN
    // ══════════════════════════════════════════════════════════════════

    /**
     * Envía la carta de aceptación a todos los registros aceptados
     * (independientemente de si ya fueron notificados) y actualiza status_notified = true.
     */
    protected function handleBulkAccepted(string $provider): int
    {
        $interviews = CatchmentInterview::where('accepted', true)
            ->get();

        $total  = $interviews->count();
        $this->info("─────────────────────────────────────────────────");
        $this->info("📬 ENVÍO MASIVO — Cartas de ACEPTACIÓN");
        $this->line("   Proveedor : {$provider}");
        $this->line("   Total     : {$total} entrevistas aceptadas");
        $this->line("   CC / BCC  : " . ($this->option('status_copy') ? 'ACTIVADO' : 'Desactivado'));
        $this->info("─────────────────────────────────────────────────");

        if ($total === 0) {
            $this->warn("No hay entrevistas aceptadas registradas.");
            return 0;
        }

        if (! $this->confirm("¿Desea continuar y enviar {$total} correo(s) de aceptación?", true)) {
            $this->warn("Operación cancelada.");
            return 0;
        }

        $this->loadSharedData();

        $sent   = 0;
        $errors = 0;

        foreach ($interviews as $interview) {
            $this->line("\n  [{$interview->id}] {$interview->student_full_name} → {$interview->email}");

            if (empty($interview->email) || ! filter_var($interview->email, FILTER_VALIDATE_EMAIL)) {
                $this->warn("      ⚠ Email inválido, se omite.");
                $errors++;
                continue;
            }

            if ($this->sendAcceptanceEmail($interview, $interview->email, real: true)) {
                // Actualizar status_notified sin disparar eventos Livewire
                $interview->status_notified = true;
                $interview->saveQuietly();

                $this->info("      ✅ Enviado y status_notified actualizado.");
                $sent++;
            } else {
                $this->error("      ❌ Error al enviar. status_notified no se modificó.");
                $errors++;
            }

            // Pausa breve entre envíos para no saturar el API
            if ($sent < $total) {
                sleep(1);
            }
        }

        $this->printBulkSummary($sent, $errors, $total);
        return $errors > 0 ? 1 : 0;
    }

    // ══════════════════════════════════════════════════════════════════
    //  MODO MASIVO: CARTAS DE LISTA EN ESPERA
    // ══════════════════════════════════════════════════════════════════

    /**
     * Envía la carta de lista en espera a todos los registros en espera
     * (independientemente de si ya fueron notificados) y actualiza status_notified = true.
     */
    protected function handleBulkStandby(string $provider): int
    {
        $interviews = CatchmentInterview::where('status_standby', true)
            ->get();

        $total = $interviews->count();
        $this->info("─────────────────────────────────────────────────");
        $this->info("📬 ENVÍO MASIVO — Cartas de LISTA EN ESPERA");
        $this->line("   Proveedor : {$provider}");
        $this->line("   Total     : {$total} entrevistas en lista de espera");
        $this->line("   CC / BCC  : " . ($this->option('status_copy') ? 'ACTIVADO' : 'Desactivado'));
        $this->info("─────────────────────────────────────────────────");

        if ($total === 0) {
            $this->warn("No hay entrevistas en lista de espera registradas.");
            return 0;
        }

        if (! $this->confirm("¿Desea continuar y enviar {$total} correo(s) de lista en espera?", true)) {
            $this->warn("Operación cancelada.");
            return 0;
        }

        $this->loadSharedData();

        $sent   = 0;
        $errors = 0;

        foreach ($interviews as $interview) {
            $this->line("\n  [{$interview->id}] {$interview->student_full_name} → {$interview->email}");

            if (empty($interview->email) || ! filter_var($interview->email, FILTER_VALIDATE_EMAIL)) {
                $this->warn("      ⚠ Email inválido, se omite.");
                $errors++;
                continue;
            }

            if ($this->sendStandbyEmail($interview, $interview->email, real: true)) {
                // Actualizar status_notified sin disparar eventos Livewire
                $interview->status_notified = true;
                $interview->saveQuietly();

                $this->info("      ✅ Enviado y status_notified actualizado.");
                $sent++;
            } else {
                $this->error("      ❌ Error al enviar. status_notified no se modificó.");
                $errors++;
            }

            if ($sent < $total) {
                sleep(1);
            }
        }

        $this->printBulkSummary($sent, $errors, $total);
        return $errors > 0 ? 1 : 0;
    }

    // ══════════════════════════════════════════════════════════════════
    //  HELPERS INTERNOS
    // ══════════════════════════════════════════════════════════════════

    /** Precarga datos comunes para no repetir queries en cada iteración del batch. */
    protected function loadSharedData(): void
    {
        $this->institucion = Institucion::orderBy('created_at', 'DESC')->first();
        $this->autoridad   = Autoridad::getTipoAuthority('1');
        $this->director    = Autoridad::getTipoAuthority('2');
    }

    /** Resuelve el servicio de email según el proveedor. */
    protected function resolveEmailService(string $provider): object
    {
        return match ($provider) {
            'resend' => app(ResendEmailService::class),
            default  => app(SendPulseService::class),
        };
    }

    /**
     * Envía la carta de aceptación.
     *
     * @param bool $real  true = envío real al destinatario original (sin prefijo [TEST])
     */
    protected function sendAcceptanceEmail(CatchmentInterview $interview, string $toEmail, bool $real = false): bool
    {
        try {
            // Si la entrevista no tiene token, generamos uno temporal en memoria
            // para que route('catchments.accepted', $token) no falle. No se persiste.
            $originalToken = $interview->token;
            if (empty($interview->token)) {
                $interview->token = $interview->generateToken();
                if (! $real) {
                    $this->line('   ℹ Token temporal generado para el render (no se guarda en BD).');
                }
            }

            $htmlContent = view('email.catchment.accepted', [
                'interview'    => $interview,
                'institucion'  => $this->institucion ?? Institucion::orderBy('created_at', 'DESC')->first(),
                'autoridad'    => $this->autoridad   ?? Autoridad::getTipoAuthority('1'),
                'director'     => $this->director    ?? Autoridad::getTipoAuthority('2'),
                'list_comment' => Catchment::COLUMN_COMMENTS,
                'toDate'       => Date::now()->format('d F Y'),
            ])->render();

            // Restaurar token para no alterar el objeto si se reutiliza
            $interview->token = $originalToken;

            $subject  = $real
                ? 'Aceptación de Solicitud - Matrícula Escolar'
                : '[TEST] Aceptación de Solicitud - Matrícula Escolar';

            $cc  = $this->option('status_copy') ? env('MAIL_CC_ADDRESS_CONTROL', null) : null;
            $bcc = $this->option('status_copy') ? [env('MAIL_CC_ADDRESS', null), env('MAIL_CC_ADDRESS_BIENESTAR', null)] : null;

            $response = $this->emailService->send($toEmail, $subject, $htmlContent, null, false, $cc, $bcc);

            if (! $response['success']) {
                $this->line('      Detalle: ' . ($response['message'] ?? 'Sin mensaje'));
                return false;
            }

            return true;
        } catch (\Exception $e) {
            $this->error('      Excepción: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Envía la carta de lista en espera.
     *
     * @param bool $real  true = envío real al destinatario original (sin prefijo [TEST])
     */
    protected function sendStandbyEmail(CatchmentInterview $interview, string $toEmail, bool $real = false): bool
    {
        try {
            $htmlContent = view('email.catchment.status_standby', [
                'interview'    => $interview,
                'institucion'  => $this->institucion ?? Institucion::orderBy('created_at', 'DESC')->first(),
                'autoridad'    => $this->autoridad   ?? Autoridad::getTipoAuthority('1'),
                'director'     => $this->director    ?? Autoridad::getTipoAuthority('2'),
                'list_comment' => Catchment::COLUMN_COMMENTS,
                'toDate'       => Date::now()->format('d F Y'),
            ])->render();

            $subject  = $real
                ? 'Lista de Espera - Matrícula Escolar'
                : '[TEST] Lista de Espera - Matrícula Escolar';

            $cc  = $this->option('status_copy') ? env('MAIL_CC_ADDRESS_CONTROL', null) : null;
            $bcc = $this->option('status_copy') ? [env('MAIL_CC_ADDRESS', null), env('MAIL_CC_ADDRESS_BIENESTAR', null)] : null;

            $response = $this->emailService->send($toEmail, $subject, $htmlContent, null, false, $cc, $bcc);

            if (! $response['success']) {
                $this->line('      Detalle: ' . ($response['message'] ?? 'Sin mensaje'));
                return false;
            }

            return true;
        } catch (\Exception $e) {
            $this->error('      Excepción: ' . $e->getMessage());
            return false;
        }
    }

    /** Imprime el resumen final de un envío masivo. */
    protected function printBulkSummary(int $sent, int $errors, int $total): void
    {
        $this->info("\n─────────────────────────────────────────────────");
        $this->info("📊 Resumen:");
        $this->line("   Total     : {$total}");
        $this->info("   Enviados  : {$sent}");
        if ($errors > 0) {
            $this->error("   Errores   : {$errors}");
        } else {
            $this->line("   Errores   : 0");
        }
        $this->info("─────────────────────────────────────────────────");
    }
}
