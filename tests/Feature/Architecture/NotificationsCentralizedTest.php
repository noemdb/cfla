<?php

namespace Tests\Feature\Architecture;

use Illuminate\Support\Str;
use Symfony\Component\Finder\Finder;
use Tests\TestCase;

/**
 * Test de arquitectura (blueprint/notifications, regla de oro §4.3): toda
 * notificación de base de datos debe emitirse por NotificationService para
 * heredar broadcast Reverb (NotificationReceived), invalidación de la caché
 * del badge y crash-guard con re-emisión. Este test evita que un emisor
 * directo vuelva a introducir silenciosamente el bug del incidente
 * toDatabase() — notificaciones persistidas pero sin push en vivo.
 *
 * Excepciones permitidas:
 *  - NotificationService (el punto central: es el único autorizado).
 *  - Envíos por mail vía Notification::route('mail', ...) (anonymous
 *    notifiable: no persisten en la tabla `notifications`, no hay broadcast
 *    que perder).
 */
class NotificationsCentralizedTest extends TestCase
{
    /** Emisores autorizados de notificaciones de base de datos. */
    private const ALLOWED_SENDERS = [
        'app/Services/NotificationService.php',
    ];

    public function test_ningun_emisor_directo_de_notificaciones_db_fuera_del_servicio(): void
    {
        $violations = [];

        $finder = (new Finder)
            ->files()
            ->in(app_path())
            ->name('*.php')
            ->notPath(str_replace('app/', '', self::ALLOWED_SENDERS));

        foreach ($finder as $file) {
            $path = 'app/'.Str::after($file->getRealPath(), app_path().DIRECTORY_SEPARATOR);
            $contents = $file->getContents();

            // Notification::send(...) / Notification::sendNow(...) — envío
            // directo por la fachada, sin broadcast ni invalidación de caché.
            if (preg_match('/Notification::(send|sendNow)\s*\(/', $contents)) {
                $violations[] = "{$path}: Notification::send()/sendNow() directo";
                continue;
            }

            // Descartar primero las cadenas permitidas: envíos por mail vía
            // anonymous notifiable. El [^;] impide cruzar el límite de la
            // sentencia, así el match no se desborda a otros statements.
            $contents = preg_replace(
                '/Notification::route\s*\([^;]*\)\s*->notify\s*\([^;]*\);/',
                '',
                $contents,
            );

            foreach (explode("\n", $contents) as $lineNo => $line) {
                // ->notify(new X) — persiste en la tabla notifications sin
                // pasar por el servicio (notifiable directo).
                if (preg_match('/->notify\s*\(\s*new\s+/', $line)) {
                    $violations[] = "{$path}:".($lineNo + 1).": ->notify(new ...) directo (notifiable)";
                }
            }
        }

        $this->assertSame([], $violations, sprintf(
            "Emisores directos de notificaciones DB encontrados (regla de oro del blueprint/"
            ."notifications §4.3):\n\n%s\n\nEnrútalos por app(NotificationService::class)"
            ."->notifyUsers(...) para heredar broadcast Reverb e invalidación de caché.",
            implode("\n", $violations),
        ));
    }
}
