<?php

namespace App\Http;

use Illuminate\Foundation\Http\Kernel as HttpKernel;

class Kernel extends HttpKernel
{
    /**
     * The application's global HTTP middleware stack.
     *
     * These middleware are run during every request to your application.
     *
     * @var array
     */
    protected $middleware = [
        \App\Http\Middleware\TrustProxies::class,
        \App\Http\Middleware\CheckForMaintenanceMode::class,
        \Illuminate\Foundation\Http\Middleware\ValidatePostSize::class,
        \App\Http\Middleware\TrimStrings::class,
        \Illuminate\Foundation\Http\Middleware\ConvertEmptyStringsToNull::class,
    ];

    /**
     * The application's route middleware groups.
     *
     * @var array
     */
    protected $middlewareGroups = [
        'web' => [
            \App\Http\Middleware\EncryptCookies::class,
            \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
            \Illuminate\Session\Middleware\StartSession::class,
            // \Illuminate\Session\Middleware\AuthenticateSession::class,
            \Illuminate\View\Middleware\ShareErrorsFromSession::class,
            \App\Http\Middleware\VerifyCsrfToken::class,
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
            \App\Http\Middleware\LogRouteAccess::class,
        ],

        'api' => [
            'throttle:60,1',
            'bindings',
        ],
    ];

    /**
     * The application's route middleware.
     *
     * These middleware may be assigned to groups or used individually.
     *
     * @var array
     */
    protected $routeMiddleware = [
        'auth' => \App\Http\Middleware\Authenticate::class,
        'auth.basic' => \Illuminate\Auth\Middleware\AuthenticateWithBasicAuth::class,
        'bindings' => \Illuminate\Routing\Middleware\SubstituteBindings::class,
        'cache.headers' => \Illuminate\Http\Middleware\SetCacheHeaders::class,
        'can' => \Illuminate\Auth\Middleware\Authorize::class,
        'guest' => \App\Http\Middleware\RedirectIfAuthenticated::class,
        'signed' => \Illuminate\Routing\Middleware\ValidateSignature::class,
        'throttle' => \Illuminate\Routing\Middleware\ThrottleRequests::class,
        'verified' => \Illuminate\Auth\Middleware\EnsureEmailIsVerified::class,
        'role' => \App\Http\Middleware\RoleMiddleware::class, //añadido - verificando
        'is_admin'=> 'App\Http\Middleware\Admin\IsAdmin',
        'is_admon'=> 'App\Http\Middleware\Admin\IsAdmon',
        'is_control'=> 'App\Http\Middleware\Admin\IsControl',
        'is_common'=> 'App\Http\Middleware\Admin\IsCommon',
        'is_profesor'=> 'App\Http\Middleware\Admin\IsProfesor',
        'is_director'=> 'App\Http\Middleware\Admin\IsDirector',
        'is_representant'=> 'App\Http\Middleware\Admin\IsRepresentant',
        'is_estudiant'=> 'App\Http\Middleware\Admin\IsEstudiant',
        // 'is_controls'=> 'App\Http\Middleware\Admin\IsControls',
        'is_admons'=> 'App\Http\Middleware\Admin\IsAdmons',
        'is_academico'=> 'App\Http\Middleware\Admin\IsAcademico',
        'is_bienestar'=> 'App\Http\Middleware\Admin\IsBienestar',
        'is_evaluacion'=> 'App\Http\Middleware\Admin\IsEvaluacion',
        'is_proyecto'=> 'App\Http\Middleware\Admin\IsProyecto',
        'is_leader'=> 'App\Http\Middleware\Admin\IsLeader',
        'is_inicial'=> 'App\Http\Middleware\Admin\IsInicial',
        'is_time'=> 'App\Http\Middleware\Admin\IsTime',
        'is_planning'=> 'App\Http\Middleware\Admin\IsPlanning',
        'is_audit'=> 'App\Http\Middleware\Admin\IsAudit',

        // "cors" => \App\Http\Middleware\Cors::class,
        // \App\Http\Middleware\CorsMiddleware::class,
        'has_any_role' => \App\Http\Middleware\HasAnyRole::class,

        'check.solvency' => \App\Http\Middleware\CheckSolvency::class, /* Estudiantes/Representantes NO solventes */

        // Middleware para manejar límites de rate para gemini api
        'gemini.rate' => \App\Http\Middleware\GeminiRateLimit::class,

    ];

    /**
     * The priority-sorted list of middleware.
     *
     * This forces non-global middleware to always be in the given order.
     *
     * @var array
     */
    protected $middlewarePriority = [
        \Illuminate\Session\Middleware\StartSession::class,
        \Illuminate\View\Middleware\ShareErrorsFromSession::class,
        \App\Http\Middleware\Authenticate::class,
        \Illuminate\Session\Middleware\AuthenticateSession::class,
        \Illuminate\Routing\Middleware\SubstituteBindings::class,
        \Illuminate\Auth\Middleware\Authorize::class,
    ];
}
