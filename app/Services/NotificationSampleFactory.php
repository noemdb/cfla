<?php

namespace App\Services;

use App\Notifications\ActivityApprovedNotification;
use App\Notifications\ActivityCreatedNotification;
use App\Notifications\BinnacleBacklogNotification;
use App\Notifications\BinnacleDailyReportNotification;
use App\Notifications\CompetitionUpdateNotification;
use App\Notifications\DiagQuestionNotification;
use App\Notifications\LessonScheduledForApproval;
use App\Notifications\PeducativoNotification;
use App\Notifications\PendingApprovalReminderNotification;
use App\Notifications\PevaluacionObservationNotification;
use App\Notifications\ProfessorMissingActivityNotification;
use App\Notifications\ReverbTestNotification;
use App\Notifications\ScheduledLessonsReminderNotification;
use App\Notifications\StaleActivitiesReminderNotification;
use App\Notifications\SubstituteAssignedNotification;
use App\Notifications\TimetableCalendarActivatedNotification;
use App\Notifications\TimetableChangedNotification;
use Illuminate\Notifications\Notification as BaseNotification;
use InvalidArgumentException;

/**
 * Construye una instancia de muestra de cualquier notificación del sistema para
 * poder dispararla desde `php artisan reverb:test --notify=<usuario>
 * --notification=<tipo>` sin tener que reproducir el flujo real que la origina
 * (registrar una actividad, activar un horario, guardar observaciones…).
 *
 * La muestra usa datos ficticios pero rutas reales (`route()`), de modo que la
 * campana del navbar la pinte exactamente igual que en producción, incluido el
 * reenvío por rol de `NotificationTargetResolver`.
 *
 * El índice de búsqueda es tolerante: acepta la FQCN, el nombre corto de la
 * clase, el `type` del payload (`activity_created`), kebab-case
 * (`activity-created`) o cualquier variante con mayúsculas/guiones.
 */
class NotificationSampleFactory
{
    /**
     * Catálogo de notificaciones con muestra disponible: alias legible => clase.
     *
     * @var array<string, class-string<BaseNotification>>
     */
    private const CATALOG = [
        'reverb_test' => ReverbTestNotification::class,
        'activity_created' => ActivityCreatedNotification::class,
        'activity_approved' => ActivityApprovedNotification::class,
        'lesson_scheduled' => LessonScheduledForApproval::class,
        'pending_approval_reminder' => PendingApprovalReminderNotification::class,
        'scheduled_lessons_reminder' => ScheduledLessonsReminderNotification::class,
        'stale_activities_reminder' => StaleActivitiesReminderNotification::class,
        'professor_missing_activity' => ProfessorMissingActivityNotification::class,
        'pevaluacion_observation' => PevaluacionObservationNotification::class,
        'peducativo' => PeducativoNotification::class,
        'diag_question' => DiagQuestionNotification::class,
        'timetable_calendar_activated' => TimetableCalendarActivatedNotification::class,
        'timetable_changed' => TimetableChangedNotification::class,
        'substitute_assigned' => SubstituteAssignedNotification::class,
        'binnacle_backlog' => BinnacleBacklogNotification::class,
        'binnacle_daily_report' => BinnacleDailyReportNotification::class,
        'competition_update' => CompetitionUpdateNotification::class,
    ];

    /**
     * Catálogo con el canal de cada tipo, para `--list-notifications`.
     *
     * @return array<string, array{class: class-string<BaseNotification>, channel: string}>
     */
    public static function catalogue(): array
    {
        $out = [];

        foreach (self::CATALOG as $alias => $class) {
            $out[$alias] = [
                'class' => $class,
                'channel' => self::channelOf($class),
            ];
        }

        return $out;
    }

    /**
     * Resuelve el alias/entrada a una clase del catálogo.
     *
     * Acepta el alias (`activity_created`), el nombre corto, la FQCN y también
     * la FQCN *sin* separadores, que es lo que llega cuando el shell se come
     * los backslashes (`App\Notifications\X` no entrecomillado → `AppNotificationsX`).
     *
     * @throws InvalidArgumentException si no hay muestra para esa entrada
     */
    public static function resolveClass(string $input): string
    {
        $needle = self::normalize($input);

        foreach (self::CATALOG as $alias => $class) {
            if (self::normalize($alias) === $needle) {
                return $class;
            }
        }

        // Por nombre de clase: exacto o como sufijo (FQCN con o sin namespace).
        // Ninguna clase del catálogo es sufijo de otra, así que el sufijo no
        // puede dar falsos positivos.
        foreach (self::CATALOG as $candidate) {
            $short = self::normalize(class_basename($candidate));

            if ($short === $needle || str_ends_with($needle, $short)) {
                return $candidate;
            }
        }

        throw new InvalidArgumentException(sprintf(
            'No hay muestra para "%s". Usa --list-notifications para ver los tipos disponibles. '
            .'Si pasaste una FQCN, entrecomíllala para que el shell no se coma los backslashes: '
            .'--notification=\'App\\Notifications\\DiagQuestionNotification\'.',
            $input
        ));
    }

    /**
     * Instancia de muestra lista para `NotificationService::notifyUsers()`.
     *
     * @param  string  $input  alias, nombre corto o FQCN de la notificación
     *
     * @throws InvalidArgumentException si no existe la muestra o si la
     *                                  notificación no usa el canal `database`
     */
    public static function make(string $input, string $recipientLabel = 'usuario'): BaseNotification
    {
        $class = self::resolveClass($input);
        $notification = self::build($class, $recipientLabel);
        $channels = self::channelsOf($notification);

        if (! in_array('database', $channels, true)) {
            throw new InvalidArgumentException(sprintf(
                '%s no usa el canal "database" (usa: %s), así que no aparecería en la campana.',
                class_basename($class),
                implode(', ', $channels) ?: 'ninguno'
            ));
        }

        return $notification;
    }

    /**
     * @param  class-string<BaseNotification>  $class
     */
    private static function build(string $class, string $recipientLabel): BaseNotification
    {
        $now = now();

        return match ($class) {
            ReverbTestNotification::class => new ReverbTestNotification(
                'Reverb: notificación de prueba para '.$recipientLabel.' ('.$now->toDateTimeString().')',
            ),

            ActivityCreatedNotification::class => new ActivityCreatedNotification(
                type: 'activity_created',
                message: 'Se registró una nueva actividad en MATEMÁTICAS · QUINTO AÑO · A (muestra de prueba).',
                url: route('app.leadership.activities'),
                activityId: 0,
                pevaluacionId: null,
                asignaturaName: 'MATEMÁTICAS',
                gradoName: 'QUINTO AÑO',
                seccionName: 'A',
            ),

            ActivityApprovedNotification::class => new ActivityApprovedNotification(
                type: 'activity_approved',
                message: 'La actividad "Ecuaciones de primer grado" fue aprobada por Planificación (muestra de prueba).',
                url: route('app.planning.activities.index'),
                activityId: 0,
                topic: 'Ecuaciones de primer grado',
                asignaturaName: 'MATEMÁTICAS',
                approverName: 'Planificación',
                approverRole: 'Planificación',
            ),

            LessonScheduledForApproval::class => new LessonScheduledForApproval(
                activityId: 0,
                teacherName: 'Prof. Juan Pérez',
                activityTitle: 'Ecuaciones de primer grado',
                scheduledAt: $now->copy()->addDay()->setTime(10, 0)->toDateTimeString(),
            ),

            PendingApprovalReminderNotification::class => new PendingApprovalReminderNotification(
                type: 'pending_approval_reminder',
                message: 'Tienes 3 actividades sin aprobar que vencen en 3 días (muestra de prueba).',
                url: route('app.planning.activities.index'),
                count: 3,
                activityIds: [],
            ),

            ScheduledLessonsReminderNotification::class => new ScheduledLessonsReminderNotification(
                message: 'Hay 5 lecciones programadas para publicación en 3 días (muestra de prueba).',
                url: route('app.planning.lms.monitor', ['filterStatus' => 'SCHEDULED']),
                count: 5,
                date: $now->copy()->addDays(3)->toDateTimeString(),
                activityIds: [],
            ),

            StaleActivitiesReminderNotification::class => new StaleActivitiesReminderNotification(
                message: 'Tus áreas llevan 9 días sin registrar actividades (muestra de prueba).',
                url: route('app.leadership.activities'),
                daysSince: 9,
                lastActivityAt: $now->copy()->subDays(9)->toDateTimeString(),
            ),

            ProfessorMissingActivityNotification::class => new ProfessorMissingActivityNotification(
                message: 'Tienes 3 pevaluaciones sin ninguna actividad registrada (muestra de prueba).',
                url: route('app.profesors.activities.index'),
                missingCount: 3,
                totalPevaluaciones: 6,
                lapsoName: '1ER MOMENTO',
                lapsoId: null,
            ),

            PevaluacionObservationNotification::class => new PevaluacionObservationNotification(
                type: 'pevaluacion_observation_updated',
                message: 'Se actualizaron las observaciones de MATEMÁTICAS · QUINTO AÑO · A (muestra de prueba).',
                url: route('app.planning.pevaluacions.index'),
                pevaluacionId: 0,
                pestudioName: 'QUINTO AÑO',
                asignaturaName: 'MATEMÁTICAS',
                seccionName: 'A',
                profesorName: 'Prof. Juan Pérez',
                lapsoName: '1ER MOMENTO',
                observationPreview: 'Septs. mejoraron la participación; revisar evidencias de la semana 3.',
                action: 'actualizó',
            ),

            PeducativoNotification::class => new PeducativoNotification(
                type: 'peducativo_updated',
                message: 'Programa Educativo "Convivencia y valores" actualizado por Planificación (muestra de prueba).',
                url: route('app.planning.peducativos.index'),
                peducativoId: 0,
                peducativoName: 'Convivencia y valores',
                action: 'actualizado',
            ),

            DiagQuestionNotification::class => new DiagQuestionNotification(
                type: 'diag_question_updated',
                message: 'Pregunta diagnóstica actualizada en MATEMÁTICAS (muestra de prueba).',
                url: route('app.planning.diagnostico.index'),
                diagQuestionId: 0,
                pensumId: null,
                asignaturaName: 'MATEMÁTICAS',
                pensumLabel: 'EDUCACION MEDIA GENERAL · QUINTO AÑO · MATEMÁTICAS',
                action: 'actualizada',
            ),

            TimetableCalendarActivatedNotification::class => new TimetableCalendarActivatedNotification(
                calendarId: 0,
                calendarName: 'Horario 1ER MOMENTO 2026-2027',
                lapsoName: '1ER MOMENTO',
                pestudioName: 'EDUCACION MEDIA GENERAL',
            ),

            TimetableChangedNotification::class => new TimetableChangedNotification(
                calendarId: 0,
                calendarName: 'Horario 1ER MOMENTO 2026-2027',
                message: 'El horario «Horario 1ER MOMENTO 2026-2027» fue modificado (muestra de prueba).',
                type: 'timetable_changed',
            ),

            SubstituteAssignedNotification::class => new SubstituteAssignedNotification(
                assignmentId: 0,
                calendarName: 'Horario 1ER MOMENTO 2026-2027',
                date: $now->toDateString(),
                periodLabel: '3ra hora',
                subjectLabel: 'MATEMÁTICAS',
            ),

            BinnacleBacklogNotification::class => new BinnacleBacklogNotification(pending: 25, threshold: 10),

            BinnacleDailyReportNotification::class => new BinnacleDailyReportNotification([
                'date' => $now->copy()->subDay()->toDateString(),
                'events' => 0,
                'errors' => 0,
            ]),

            CompetitionUpdateNotification::class => new CompetitionUpdateNotification([
                'competition_id' => 0,
                'message' => 'Actualización de competencia (muestra de prueba).',
            ]),

            default => throw new InvalidArgumentException(
                'La clase '.$class.' está en el catálogo pero no tiene muestra definida.'
            ),
        };
    }

    /**
     * Canal declarado por la notificación, para el listado de tipos. Se lee de
     * una muestra real (`build`) en lugar de inventar argumentos: así el canal
     * que muestra `--list-notifications` es el que la clase declara de verdad.
     *
     * @param  class-string<BaseNotification>  $class
     */
    private static function channelOf(string $class): string
    {
        try {
            $channels = self::channelsOf(self::build($class, 'muestra'));
        } catch (\Throwable $e) {
            return '? ('.$e->getMessage().')';
        }

        return implode(', ', $channels) ?: '?';
    }

    /**
     * @return array<int, string>
     */
    private static function channelsOf(BaseNotification $notification): array
    {
        return array_map('strval', (array) $notification->via(new \App\Models\User));
    }

    private static function normalize(string $value): string
    {
        return (string) preg_replace('/[^a-z0-9]/i', '', $value);
    }
}
