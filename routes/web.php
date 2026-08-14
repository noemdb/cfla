<?php

use App\Http\Controllers\Admin\VotingDashboardController;
use App\Http\Controllers\Admin\VotingPollController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\CensusController;
use App\Http\Controllers\Census\CatchmentPDFController;
use App\Http\Controllers\Educational\CompetitionController;
use App\Http\Controllers\GmailController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\Planning\PlanningController;
use App\Http\Controllers\PollVotingController;
use App\Http\Controllers\VotingFingerprintController;
use Illuminate\Support\Facades\Route;
use App\Livewire\Admin\Diagnostic\IndexComponent as DiagnosticIndex;
use App\Livewire\Admin\Educational\Competition\IndexComponent as CompetitionIndex;
use App\Livewire\Bot\IndexComponent as BotIndex;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// ===================================================
// RUTAS NORMALES DE LA APLICACIÓN
// ===================================================

Route::get('/studia', [HomeController::class, 'studia'])->name('studia');
Route::get('/diagnostico', [HomeController::class, 'diagnostico'])->name('diagnostico');

Route::get('/', [HomeController::class, 'home'])->name('home');
Route::get('/matricula', [HomeController::class, 'enrollment'])->name('enrollment');
Route::get('/pago', [HomeController::class, 'credicard'])->name('credicard');
Route::get('/post/{id}', [HomeController::class, 'post'])->name('post');

Route::get('/bot', BotIndex::class)->name('bot.index');

Route::get('/censo', [CensusController::class, 'index'])->name('census');
Route::get('/catchment/download-pdf/{token}', [CatchmentPDFController::class, 'downloadPDF'])->name('catchment.download.pdf');
Route::get('/catchment/preview', [CatchmentPDFController::class, 'preview'])->name('catchment.preview');

// Ruta para la prosecución
Route::get('/prosecucion', [HomeController::class, 'prosecucion'])->name('prosecucion');
Route::get('/prosecucion/guia', [HomeController::class, 'prosecucion_guia'])->name('prosecucion_guia');
Route::get('/prosecucion/download/{id}', [HomeController::class, 'downloadProsecucionPDF'])->name('prosecucion.download.pdf');

Route::group(['prefix' => 'general', 'namespace' => 'General'], function () {
    Route::get('/educational/competition/moderator/{token}', [CompetitionController::class, 'moderator'])->name('general.educational.competition.moderator');
    Route::get('/educational/competition/board/{token}', [CompetitionController::class, 'board'])->name('general.educational.competition.board');
    Route::get('/educational/competition/scoreboard/{token}', [CompetitionController::class, 'scoreboard'])->name('general.educational.competition.scoreboard');
});

// Route::put('/competitions/{orderId}/status', [OrderController::class, 'updateOrderStatus']);
Route::get('/competitions/{orderId}/status/{status}', [OrderController::class, 'updateOrderStatus']);

//Api Gmail
Route::get('/auth/google', [GmailController::class, 'redirectToGoogle'])->name('google.auth');
Route::get('/oauth2callback', [GmailController::class, 'handleGoogleCallback'])->name('google.callback');
Route::get('/send-email', [GmailController::class, 'sendEmail']);

/////////////////////////////////////////////////////////////
//////////////// Encuestas Anonimas /////////////////////////

// Rutas públicas (activas) de votación
// Route::get('/voting/index', [PollVotingController::class, 'index'])->name('poll.voting.index');

// Ruta para el asistente de votación
Route::get('/voting/asistent', [PollVotingController::class, 'asistent'])
    ->name('voting.asistent')
    ->middleware('throttle:voting-asistent'); // limitar el peticiones por IP

// Ruta para guía del módulo de votación
Route::get('/voting/guia', [PollVotingController::class, 'guia'])->name('voting.guia');

Route::get('/voting/proposal', [PollVotingController::class, 'guia'])->name('voting.proposal');

// Ruta para índice de votación
// Route::get('/voting', [PollVotingController::class, 'index'])->name('voting.index');

// Ruta para votacion, sin verificaciones de unicidad de voto
Route::get('/poll/voting/{access_token}', [PollVotingController::class, 'show'])->name('poll.voting.show');

// Nueva ruta para resultados de encuesta
Route::get('/poll/voting/result/{access_token}', [PollVotingController::class, 'result'])->name('poll.voting.result');

// Ruta para resultados de todas las encuestas
Route::get('/voting/results', [PollVotingController::class, 'results'])->name('voting.results');

Route::get('/poll/qr/{uuid}', [PollVotingController::class, 'showQR'])->name('poll.qr.show');
Route::get('/poll/participation/{uuid}', [PollVotingController::class, 'showParticipation'])->name('poll.participation.show');

// Rutas del panel administrativo
Route::prefix('admin')->name('admin.')->middleware(['auth'])->group(function () {

    // Rutas protegidas para Administradores y Personal de Diagnóstico
    Route::middleware(['isAdminOrDiagnostic'])->group(function () {

        Route::get('/', function () {
            return view('admin.index');
        })->name('index');

        // Módulo de Votación
        Route::prefix('voting')->name('voting.')->group(function () {
            Route::get('/dashboard', [VotingDashboardController::class, 'index'])->name('dashboard');
            Route::resource('polls', VotingPollController::class);
            Route::post('/polls/{poll}/start', [VotingPollController::class, 'start'])->name('polls.start');
            Route::post('/polls/{poll}/stop', [VotingPollController::class, 'stop'])->name('polls.stop');
            Route::post('/polls/{poll}/reset', [VotingPollController::class, 'reset'])->name('polls.reset');
            Route::get('/results', [VotingPollController::class, 'results'])->name('results');
            Route::get('/list', [VotingPollController::class, 'publicList'])->name('list');
        });

        // Módulo de Diagnóstico
        Route::prefix('diagnostico')->name('diagnostico.')->group(function () {
            Route::get('/', DiagnosticIndex::class)->name('index');
        });

        // Módulo de Competiciones Académicas
        Route::prefix('educational')->name('educational.')->group(function () {
            Route::get('/competition', CompetitionIndex::class)->name('competition.index');
            Route::get('/competition/{token}/answers', [CompetitionController::class, 'answers'])->name('competition.answers');
        });

        // Módulo de Usuarios
        Route::get('/users', \App\Livewire\Admin\Users\IndexComponent::class)->name('users.index');
    });

    // Rutas exclusivas para Administradores
    Route::middleware(['isAdmin'])->group(function () {
        Route::get('logs', \App\Livewire\Admin\Logs\IndexComponent::class)->name('logs');
        Route::get('database/backup', [\App\Http\Controllers\Admin\DatabaseController::class, 'downloadBackup'])->name('database.backup');
    });
});

// ===================================================
// MÓDULOS /app (Planificación, Profesor, etc.)
// ===================================================
Route::prefix('app')->name('app.')->group(function () {

    // ───────────────────────────────────────────────
    // NOTIFICACIONES (todos los roles autenticados)
    // ───────────────────────────────────────────────
    Route::prefix('notificaciones')->middleware(['auth'])->name('notifications.')->group(function () {
        Route::get('/', \App\Livewire\App\Notifications\NotificationsIndex::class)
            ->name('index');   // nombre completo: app.notifications.index
    });

    // ───────────────────────────────────────────────
    // MÓDULO DE PLANIFICACIÓN
    // ───────────────────────────────────────────────
    Route::prefix('planning')->middleware(['auth', 'isPlanner'])->name('planning.')->group(function () {
        Route::get('/', [PlanningController::class, 'index'])->name('index');

        // Módulo de Indicadores de Planificación
        Route::prefix('indicators')->name('indicators.')->group(function () {
            Route::get('/', \App\Livewire\Planning\Indicator\IndexComponent::class)->name('index');
        });

        // Módulo de Diagnóstico
        Route::prefix('diagnostico')->name('diagnostico.')->group(function () {
            Route::get('/', function () {
                return view('planning.diagnostic.index');
            })->name('index');
            Route::get('/referents', \App\Livewire\Planning\Diagnostic\ReferentsMain::class)->name('referents.index');
        });

        // Módulo de Competiciones Académicas
        Route::prefix('educational')->name('educational.')->group(function () {
            Route::get('/competition', CompetitionIndex::class)->name('competition.index');
            Route::get('/competition/{token}/answers', [CompetitionController::class, 'answers'])->name('competition.answers');
        });

        // Módulo de Carga Académica (Pevaluacions)
        Route::prefix('pevaluacions')->name('pevaluacions.')->group(function () {
            Route::get('/', \App\Livewire\Planning\Pevaluacion\IndexComponent::class)->name('index');
        });

        // Módulo de Actividades de Planificación
        Route::prefix('activities')->name('activities.')->group(function () {
            Route::get('/', \App\Livewire\Planning\Activities\IndexComponent::class)->name('index');
            // Rutas PDF (controlador propio)
            Route::get('/format/{pevaluacion}', [\App\Http\Controllers\Planning\ActivityPdfController::class, 'format'])->name('format');
            Route::get('/resume/{pevaluacion}', [\App\Http\Controllers\Planning\ActivityPdfController::class, 'resume'])->name('resume');
        });

        // Módulo de Planes de Estudio
        Route::prefix('pestudios')->name('pestudios.')->group(function () {
            Route::get('/', \App\Livewire\Planning\Pestudio\IndexComponent::class)->name('index');
        });

        // Módulo de Áreas de Conocimiento
        Route::prefix('area-conocimientos')->name('area-conocimientos.')->group(function () {
            Route::get('/', \App\Livewire\Planning\AreaConocimiento\IndexComponent::class)->name('index');
        });

        // Módulo de Programas Educativos
        Route::prefix('peducativos')->name('peducativos.')->group(function () {
            Route::get('/', \App\Livewire\Planning\Peducativo\IndexComponent::class)->name('index');
        });

        // Módulo de Asignaturas
        Route::prefix('asignaturas')->name('asignaturas.')->group(function () {
            Route::get('/', \App\Livewire\Planning\Asignatura\IndexComponent::class)->name('index');
        });

        // Módulo de Grados
        Route::prefix('grados')->name('grados.')->group(function () {
            Route::get('/', \App\Livewire\Planning\Grado\IndexComponent::class)->name('index');
        });

        // Módulo de Secciones
        Route::prefix('secciones')->name('secciones.')->group(function () {
            Route::get('/', \App\Livewire\Planning\Seccion\IndexComponent::class)->name('index');
        });

        // Módulo de Lapsos
        Route::prefix('lapsos')->name('lapsos.')->group(function () {
            Route::get('/', \App\Livewire\Planning\Lapso\IndexComponent::class)->name('index');
        });

        // Módulo de Pensums (Pivote central: Pestudio × Grado × Asignatura)
        Route::prefix('pensums')->name('pensums.')->group(function () {
            Route::get('/', \App\Livewire\Planning\Pensum\IndexComponent::class)->name('index');
        });

        // Módulo de Profesores
        Route::prefix('profesors')->name('profesors.')->group(function () {
            Route::get('/', \App\Livewire\Planning\Profesor\IndexComponent::class)->name('index');
        });

        // Módulo de Inscripciones
        Route::prefix('inscripcions')->name('inscripcions.')->group(function () {
            Route::get('/', \App\Livewire\Planning\Inscripcion\IndexComponent::class)->name('index');
        });

        // ─── LMS: Monitor y Auditoría para Coordinadores ──────────────
        Route::prefix('lms')->name('lms.')->group(function () {
            Route::get('/monitor', \App\Livewire\Planning\Lms\LmsMonitor::class)->name('monitor');
            Route::get('/activity/{activity}/logs', \App\Livewire\Planning\Lms\ActivityAudit::class)->name('activity.audit');
            Route::get('/activity/{activity}/preview', \App\Livewire\Planning\Lms\LmsLessonViewer::class)->name('preview');

            // Impresión de lecciones LMS (Mermaid/KaTeX renderizado en el navegador).
            // Reutiliza el mismo controlador de la Dirección: el botón "Ver / Imprimir"
            // del monitor lleva los filtros activos como query string (asignatura/status
            // incluidos) y el membrete se adapta al módulo de origen.
            Route::get('/print', [
                \App\Http\Controllers\Director\LessonsPrintController::class, 'index'
            ])->name('print');   // nombre completo: app.planning.lms.print
        });

        // Diagramas de flujo: hub e infografías (documentos estáticos).
        // Cada archivo `docs/infografia/flujo{Studly}.html` se publica como
        // /app/planning/diagram/flow/{slug}. Protegido por el middleware del
        // grupo planning (auth + isPlanner).
        Route::get('/flow', [\App\Http\Controllers\Planning\FlowDiagramController::class, 'index'])
            ->name('flow.index');
        Route::get('/diagram/flow/{diagram}', [\App\Http\Controllers\Planning\FlowDiagramController::class, 'show'])
            ->name('diagram.flow.show');
    });  // ← cierra el grupo planning

    // ─── MÓDULO DE COORDINACIÓN ──────────────────────────────────
    Route::prefix('coordinacion')
        ->middleware(['auth', 'isCoordinacion'])
        ->name('coordinacion.')
        ->group(function () {

        Route::get('/', \App\Livewire\Coordinacion\IndicatorDashboard::class)
            ->name('index');

        Route::get('/pensums', \App\Livewire\Coordinacion\PensumList::class)
            ->name('pensums');

        Route::get('/carga-academica', \App\Livewire\Coordinacion\CargaAcademicaList::class)
            ->name('carga-academica');

        Route::get('/activities', \App\Livewire\Coordinacion\ActivityList::class)
            ->name('activities');
        Route::get('/activities/format/{pevaluacion}', [
            \App\Http\Controllers\Planning\ActivityPdfController::class, 'format'
        ])->name('activities.format');
        Route::get('/activities/resume/{pevaluacion}', [
            \App\Http\Controllers\Planning\ActivityPdfController::class, 'resume'
        ])->name('activities.resume');

        Route::get('/lecciones', \App\Livewire\Coordinacion\LessonList::class)
            ->name('lessons');

        // Impresión de lecciones LMS: reusa Director\LessonsPrintController; el
        // scope (peducativos del coordinador) lo deduce el controlador por el
        // nombre de ruta (patrón ADR-006).
        Route::get('/lecciones/print', [
            \App\Http\Controllers\Director\LessonsPrintController::class, 'index'
        ])->name('lessons.print');

        Route::get('/recursos', \App\Livewire\Coordinacion\ResourceList::class)
            ->name('resources');
        Route::get('/profesores', \App\Livewire\Coordinacion\ProfesorList::class)
            ->name('profesores');
    });

    // ─── Leadership: Seguimiento Jefes de Área ────────────────────
    Route::prefix('leadership')
        ->middleware(['auth', 'isLeadership'])
        ->name('leadership.')
        ->group(function () {
            // Dashboard con KPIs globales
            Route::get('/dashboard', \App\Livewire\Leadership\IndicatorDashboard::class)
                ->name('dashboard');

            // Activities (reuso IndexComponent scoped)
            Route::get('/activities', \App\Livewire\Leadership\ActivityOverview::class)
                ->name('activities');
            Route::get('/activities/format/{pevaluacion}', [
                \App\Http\Controllers\Planning\ActivityPdfController::class, 'format'
            ])->name('activities.format');
            Route::get('/activities/resume/{pevaluacion}', [
                \App\Http\Controllers\Planning\ActivityPdfController::class, 'resume'
            ])->name('activities.resume');

            // Lecciones LMS por área
            Route::get('/lessons', \App\Livewire\Leadership\LessonMonitor::class)
                ->name('lessons');

            // Impresión de lecciones LMS: reusa Director\LessonsPrintController;
            // el scope (áreas asignadas al jefe) lo deduce el controlador por el
            // nombre de ruta (patrón ADR-006).
            Route::get('/lessons/print', [
                \App\Http\Controllers\Director\LessonsPrintController::class, 'index'
            ])->name('lessons.print');

            // Vista previa de actividad LMS (independiente del módulo planning)
            Route::get('/lms/activity/{activity}/preview', function (\App\Models\app\Academy\Activity $activity) {
                return view('livewire.leadership.lms-activity-preview', compact('activity'));
            })->name('lms.preview');

            // Profesores con KPIs
            Route::get('/profesores', \App\Livewire\Leadership\ProfesorIndicators::class)
                ->name('profesores');
        });

    // ─── Dirección: Supervisión y Seguimiento (READ-ONLY) ─────────
    Route::prefix('director')
        ->middleware(['auth', 'isDirector'])
        ->name('director.')
        ->group(function () {

        // Dashboard con indicadores globales
        Route::get('/', \App\Livewire\Director\IndicatorDashboard::class)
            ->name('index');

        // Información Académica: Pensums
        Route::get('/pensums', \App\Livewire\Director\PensumList::class)
            ->name('pensums');

        // Carga Académica (Pevaluacions)
        Route::get('/carga-academica', \App\Livewire\Director\CargaAcademicaList::class)
            ->name('carga-academica');

        // Actividades de Planificación (SÓLO VISUALIZACIÓN + PDF)
        Route::get('/activities', \App\Livewire\Director\ActivityList::class)
            ->name('activities');
        Route::get('/activities/format/{pevaluacion}', [
            \App\Http\Controllers\Planning\ActivityPdfController::class, 'format'
        ])->name('activities.format');
        Route::get('/activities/resume/{pevaluacion}', [
            \App\Http\Controllers\Planning\ActivityPdfController::class, 'resume'
        ])->name('activities.resume');

        // Lecciones LMS
        Route::get('/lecciones', \App\Livewire\Director\LessonList::class)
            ->name('lessons');

        // Impresión de lecciones LMS (Mermaid/KaTeX renderizado en el navegador;
        // misma semántica de filtros que el listado; SOLO LECTURA)
        Route::get('/lecciones/print', [
            \App\Http\Controllers\Director\LessonsPrintController::class, 'index'
        ])->name('lessons.print');

        // Recursos Compartidos
        Route::get('/recursos', \App\Livewire\Director\ResourceList::class)
            ->name('resources');

        // Seguimiento Docente (KPIs)
        Route::get('/profesores', \App\Livewire\Director\ProfesorIndicators::class)
            ->name('profesores');
    });

    // ───────────────────────────────────────────────
    // MÓDULO DE PROFESOR (Dashboard)
    // ───────────────────────────────────────────────
    Route::prefix('profesors')->middleware(['auth', 'isProfesor'])->name('profesors.')->group(function () {
        Route::get('/home', [\App\Http\Controllers\Profesor\HomeController::class, 'home'])->name('home');
        Route::get('/users', [\App\Http\Controllers\Profesor\HomeController::class, 'users'])->name('users.index');

        // Módulo de Actividades
        Route::prefix('activities')->name('activities.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Profesor\ActivityController::class, 'index'])->name('index');
            Route::get('/create/{pevaluacion}', [\App\Http\Controllers\Profesor\ActivityController::class, 'create'])->name('create');
            Route::get('/format/{pevaluacion}', [\App\Http\Controllers\Profesor\ActivityController::class, 'format'])->name('format');
            Route::get('/resume/{pevaluacion}', [\App\Http\Controllers\Profesor\ActivityController::class, 'resume'])->name('resume');
            Route::get('/grados-by-pestudio/{pestudio_id}', [\App\Http\Controllers\Profesor\ActivityController::class, 'gradosByPestudio'])
                ->name('grados.by.pestudio')
                ->where('pestudio_id', '[0-9]+');
            Route::get('/secciones-by-grado/{grado_id}', [\App\Http\Controllers\Profesor\ActivityController::class, 'seccionesByGrado'])
                ->name('secciones.by.grado')
                ->where('grado_id', '[0-9]+');
        });

        // Módulo de Competencias (Debates Educativos)
        Route::prefix('competitions')->name('competitions.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Profesor\DebateController::class, 'competitions'])->name('index');
        });

        // Módulo de Diagnósticos
        Route::prefix('diagnostics')->name('diagnostics.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Profesor\DiagnosticController::class, 'index'])->name('index');
        });

        // ─── LMS: Editor de Contenido del Profesor ─────────────────
        Route::prefix('lms')->name('lms.')->group(function () {
            Route::get('/activity/lesson/new', \App\Livewire\Profesor\Lms\LessonWizard::class)
                 ->name('lesson.wizard');
            Route::get('/activity/{activity}', \App\Livewire\Profesor\Lms\ActivityEditor::class)
                 ->name('editor');
            Route::get('/comments', \App\Livewire\Profesor\Lms\CommentModeration::class)
                 ->name('comments');
            Route::get('/lessons/print', [\App\Http\Controllers\Profesor\Lms\LessonsPrintController::class, 'index'])
                 ->name('lessons.print');
        });
    });
});

// ─── LMS: Rutas de Estudiante ───────────────────────────────────────────────
Route::prefix('app/estudiante')->name('student.lms.')->middleware(['auth', 'isStudent'])->group(function () {
    Route::get('/home', \App\Livewire\Student\Lms\StudentHome::class)->name('home');
    Route::get('/perfil', \App\Livewire\Student\Lms\Profile::class)->name('profile');
    Route::get('/academica', \App\Livewire\Student\Lms\AcademicInfo::class)->name('academic');
    Route::get('/lecciones', \App\Livewire\Student\Lms\LessonList::class)->name('lessons');
    Route::get('/recursos', \App\Livewire\Student\Lms\ResourceList::class)->name('resources');
    Route::get('/activity/{activity}', \App\Livewire\Student\Lms\ActivityView::class)->name('activity');
    Route::get('/activity/{activity}/print', [\App\Http\Controllers\Lms\ActivityPrintController::class, 'show'])->name('activity.print');
    Route::get('/resource/{resource}/download', [
        \App\Http\Controllers\Lms\ResourceDownloadController::class, 'download'
    ])->name('resource.download');
});

// API para fingerprinting
Route::post('/voting/store-fingerprint', [VotingFingerprintController::class, 'store'])
    ->name('voting.store-fingerprint');

// Rutas de Perfil de Usuario
Route::middleware('auth')->group(function () {
    Route::get('/perfil', [App\Http\Controllers\ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/perfil', [App\Http\Controllers\ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/perfil', [App\Http\Controllers\ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Mostrar formulario de login
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');

// Procesar login
Route::post('/login', [LoginController::class, 'login']);

// Logout
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
