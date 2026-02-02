<?php

use App\Http\Controllers\Census\CatchmentPDFController;
use App\Http\Controllers\CensusController;
use App\Http\Controllers\Educational\CompetitionController;
use App\Http\Controllers\GmailController;
use App\Http\Controllers\HomeController;
use App\Livewire\EnrollmentWizard;
use Illuminate\Support\Facades\Route;
use Livewire\Livewire;
use App\Http\Controllers\OrderController;

use App\Http\Controllers\Admin\VotingDashboardController;
use App\Http\Controllers\Admin\VotingPollController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\PollVotingController;
use App\Http\Controllers\VotingFingerprintController;
use App\Models\VotingPoll;

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

// Route::get('/', function () {
//     return view('welcome');
// });
// Route::get('/', function () { return view('home'); });

<?php

use Illuminate\Support\Facades\Route;

// ===================================================
// BLOQUEO DE PARÁMETROS MALICIOSOS - ALTA PRIORIDAD
// ===================================================
Route::any('{any}', function () {
    $request = request();
    
    // Parámetros a bloquear
    $blockedParams = ['wanna_play_with_me', 'img', 'download', 'zip', 'cmd'];
    
    foreach ($blockedParams as $param) {
        if ($request->has($param)) {
            \Log::warning('Intento de acceso con parámetro bloqueado: ' . $param, [
                'ip' => $request->ip(),
                'url' => $request->fullUrl(),
                'user_agent' => $request->userAgent()
            ]);
            abort(403, 'Acceso denegado');
        }
    }
    
    // Bloquear contenido malicioso en cualquier parámetro
    $allInput = $request->all();
    foreach ($allInput as $value) {
        if (is_string($value) && (
            stripos($value, 'base64_decode') !== false ||
            stripos($value, 'shell_exec') !== false ||
            stripos($value, 'passthru') !== false
        )) {
            \Log::critical('Contenido malicioso detectado: ' . substr($value, 0, 100), [
                'ip' => $request->ip(),
                'url' => $request->fullUrl()
            ]);
            abort(403, 'Acceso denegado');
        }
    }
})->where('any', '.*');

// ===================================================
// RUTAS NORMALES DE LA APLICACIÓN
// ===================================================


Route::get('/studia', [HomeController::class, 'studia'])->name('studia');
Route::get('/diagnostico', [HomeController::class, 'diagnostico'])->name('diagnostico');


Route::get('/', [HomeController::class, 'home'])->name('home');
Route::get('/reporte', [HomeController::class, 'payment'])->name('payment');
Route::get('/matricula', [HomeController::class, 'enrollment'])->name('enrollment');
Route::get('/pago', [HomeController::class, 'credicard'])->name('credicard');
Route::get('/post/{id}', [HomeController::class, 'post'])->name('post');

Route::get('/censo', [CensusController::class, 'index'])->name('census');
Route::get('/catchment/download-pdf/{token}', [CatchmentPDFController::class, 'downloadPDF'])->name('catchment.download.pdf');

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

Route::get('/admin', function () {
    return view('admin.index');
})->middleware(['auth', 'isAdmin'])->name('admin.index');

// Rutas del panel administrativo para los voting
Route::prefix('admin/voting')->name('admin.voting.')->middleware(['auth', 'isAdmin'])->group(function () {
    Route::get('/dashboard', [VotingDashboardController::class, 'index'])
        ->name('dashboard');

    Route::resource('polls', VotingPollController::class);

    Route::post('/polls/{poll}/start', [VotingPollController::class, 'start'])
        ->name('polls.start');
    Route::post('/polls/{poll}/stop', [VotingPollController::class, 'stop'])
        ->name('polls.stop');

    Route::post('/polls/{poll}/reset', [VotingPollController::class, 'reset'])
        ->name('polls.reset');

    // Ruta para resultados
    Route::get('/results', function () {
        $polls = VotingPoll::with(['options.votes'])
            ->withVotesCount()
            ->get();
        return view('admin.voting.polls.results', compact('polls'));
    })->name('results');

    // Ruta para mostrar encuestas públicas
    Route::get('/list', function () {
        // $polls = VotingPoll::query()
        $polls = VotingPoll::where('enable', true)
            ->with('options')
            ->withVotesCount()
            ->get();

        return view('admin.voting.polls.list', compact('polls'));
    })->name('list');
});

Route::prefix('admin')->name('admin.logs')->middleware(['auth', 'isAdmin'])->group(function () {
    Route::get('logs', '\Rap2hpoutre\LaravelLogViewer\LogViewerController@index');
});

// API para fingerprinting
Route::post('/voting/store-fingerprint', [VotingFingerprintController::class, 'store'])
    ->name('voting.store-fingerprint');


// Mostrar formulario de login
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');

// Procesar login
Route::post('/login', [LoginController::class, 'login']);

// Logout
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');



