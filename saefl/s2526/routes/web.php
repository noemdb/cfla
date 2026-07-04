<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

use App\Http\Controllers\ActivityLogController;
use App\Http\Controllers\Admin\Fix\DB\HomeController;
use App\Http\Controllers\Restapi\Exchange\GoutteController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['auth', 'is_admin']], function () {
    Route::get('/gemini-test', function () {
        return view('gemini-test');
    });
});

require __DIR__ . '/helpers/administracion.php'; // guias instruccionales
require __DIR__ . '/helpers/evaluacion.php';
require __DIR__ . '/helpers/profesors.php';

require __DIR__ . '/general/boletin.php'; // manejo del envio/mostrar informes de notas ()

// require(__DIR__ . '/meta/webhook.php'); // nanejo de webhook
// require(__DIR__ . '/meta/whatsapp.php'); // para pruebas WBA API
//Integración Meta.Business.Facebook
// Route::group(['middleware' => ['auth', 'is_admin']], function () {
//     require(__DIR__ . '/app/whatsapp/meta.php');
// });

Route::get('/', function () {
    return view('censo');
})->name('censo');

Route::redirect('/matriculations', env('APP_URL_PORTAL') . '/censo')->name('catchments.matriculations.censo');

// rutas para Planificación
Route::group(['prefix' => 'app', 'middleware' => ['auth'], 'namespace' => 'Planning'], function () {
    Route::group(['prefix' => 'plannings', 'middleware' => ['is_planning']], function () {
        require (__DIR__ . '/app/plannings.php');
    });
});

// rutas para Auditoria
Route::group(['prefix' => 'app', 'middleware' => ['auth'], 'namespace' => 'Audit'], function () {
    Route::group(['prefix' => 'audits', 'middleware' => ['is_audit']], function () {
        require (__DIR__ . '/app/audits.php');
    });
});

// rutas para Planning
Route::group(['prefix' => 'app', 'middleware' => ['auth'], 'namespace' => 'Inicial'], function () {
    Route::group(['prefix' => 'inicials', 'middleware' => ['is_inicial']], function () {
        require (__DIR__ . '/app/inicials.php');
    });
});

Route::get('/goutte', [GoutteController::class, 'getExchanRateToday']);
Route::get('/goutte/set', [GoutteController::class, 'setExchangeRateTodateCFLA']);

Route::get('/', function () {
    return view('welcome');
})->name('welcome');

Auth::routes(['register' => false]);

//INI moviles
Route::group(['prefix' => 'movile', 'namespace' => 'Moviles'], function () {
    Route::get('/android/welcome', function () {
        return view('movile.android.welcome');
    })->name('movile.android.welcome');
    Route::get('/android/catchments', function () {
        return view('movile.android.catchments');
    })->name('movile.android.catchments');
    Route::get('/android/payment', function () {
        return view('movile.android.payment');
    })->name('movile.android.payment');
    Route::get('/android/report', function () {
        return view('movile.android.report');
    })->name('movile.android.report');

    // Rutas protegidas con autenticación
    Route::group(['middleware' => ['auth']], function () {

        Route::group(['middleware' => ['is_admin']], function () {
            Route::get('/android/admin', function () {
                return view('movile.android.admin');
            })->name('movile.android.admin');
        });

        Route::get('/android/representant', function () {
            return view('movile.android.representant');
        })->name('movile.android.representant');

        Route::get('/android/director', function () {
            return view('movile.android.director');
        })->name('movile.android.director');

        Route::get('/android/bienestar', function () {
            return view('movile.android.bienestar');
        })->name('movile.android.bienestar');

        Route::get('/android/profesor', function () {
            return view('movile.android.profesor');
        })->name('movile.android.profesor');

        Route::get('/android/evaluacion', function () {
            return view('movile.android.evaluacion');
        })->name('movile.android.evaluacion');

        Route::get('/android/poll', function () {
            return view('movile.android.poll');
        })->name('movile.android.poll');

        Route::get('/android/bot', function () {
            return view('movile.android.bot');
        })->name('movile.android.bot');

        Route::get('/android/auth/password/reset', function () {
            return view('movile.android.passwordreset');
        })->name('movile.android.password.reset');

        Route::get('movile/android/competitions/general', function () {
            return view('movile.android.competitions');
        })->name('movile.android.competitions.general');

        Route::get('/android/competition', function () {
            return view('movile.android.module.competitions.index');
        })->name('movile.android.competitions');
    });

    // fin

    Route::post('/android/login', 'AuthCustomController@login')->name('movile.android.login');
    Route::post('/android/logout', 'AuthCustomController@logout')->name('movile.android.logout');
});
//FIN moviles

//Genenral
require __DIR__ . '/app/general.php';

Route::get('service/payment/button/credicard', function () {
    return view('service.payment.button.credicard.index');
})->name('service.payment.button.credicard.index');

Route::group(['prefix' => 'tester', 'middleware' => ['auth', 'is_admin']], function () {
    require (__DIR__ . '/admin/test/mailer.php');
    require (__DIR__ . '/admin/test/coll_political.php');
    require (__DIR__ . '/admin/test/exchange.php');
});

Route::get('/home', 'HomeController@index')->name('home');
Route::get('/dashboard', 'HomeController@dashboard')->name('dashboard');

//rutas para especiales solo para bienestars
Route::group(['prefix' => 'app', 'middleware' => ['auth'], 'namespace' => 'Bienestar'], function () {
    Route::group(['prefix' => 'bienestars', 'middleware' => ['is_bienestar']], function () {
        Route::get('/home', 'HomeController@home')->name('bienestars.home');
        Route::get('/indicators', 'HomeController@indicators')->name('bienestars.indicators');
        require (__DIR__ . '/app/bienestars.php');
    });
});

//rutas para administracion
Route::group(['prefix' => 'app', 'middleware' => ['auth'], 'namespace' => 'Administracion'], function () {

    Route::group(['middleware' => ['is_common']], function () {
        Route::get('/home', 'HomeController@home')->name('app.home');
    });

    //rutas comunes para todos los usuarios de ADMON y CONTROL
    Route::group(['prefix' => 'common', 'middleware' => ['is_common']], function () {
        Route::get('/home', 'HomeController@home')->name('administracion.home');
        Route::get('/dashboard', 'HomeController@dashboard')->name('administracion.dashboard');
        Route::get('/users', 'HomeController@users')->name('administracion.users.index');
        require (__DIR__ . '/app/common.php');
    });

    //rutas para los roles de el area de administracion
    Route::group(['prefix' => 'admon', 'middleware' => ['is_admon']], function () {
        require (__DIR__ . '/app/admon.php');
    });

    //rutas para los roles de control de estudios
    Route::group(['prefix' => 'control', 'middleware' => ['is_control']], function () {
        require (__DIR__ . '/app/control.php');
    });

    //rutas para admin
    Route::group(['prefix' => 'admin', 'middleware' => ['is_admin']], function () {
        require (__DIR__ . '/app/admin.php');
    });
});

// Diagnostic Reports - Accessible to Profesors, Coordinators, and Directors
Route::group(['prefix' => 'app/diagnostic', 'middleware' => ['auth'], 'namespace' => 'Diagnostic'], function () {
    Route::get('/reports', 'DiagReportController@index')->name('diagnostic.reports.index');
    Route::get('/reports/{id}', 'DiagReportController@show')->name('diagnostic.reports.show');
    Route::post('/reports/{id}/approve', 'DiagReportController@approve')->name('diagnostic.reports.approve');
    Route::post('/reports/{id}/sign', 'DiagReportController@sign')->name('diagnostic.reports.sign');
    Route::get('/reports/compare', 'DiagReportController@compare')->name('diagnostic.reports.compare');
});

//ruras ajax comunes para todas las areas y roles
Route::group(['prefix' => 'common', 'middleware' => ['auth']], function () {
    require (__DIR__ . '/app/ajax.php');
});

//rutas para especiales solo para profesors
Route::group(['prefix' => 'app', 'middleware' => ['auth'], 'namespace' => 'Profesor'], function () {

    //rutas para profesor
    Route::group(['prefix' => 'profesors', 'middleware' => ['is_profesor']], function () {
        Route::get('/home', 'HomeController@home')->name('profesors.home');
        require (__DIR__ . '/app/profesors.php');
        Route::get('/users', 'HomeController@users')->name('profesors.users.index');
    });
});

//rutas para especiales solo para directors
Route::group(['prefix' => 'app', 'middleware' => ['auth'], 'namespace' => 'Director'], function () {
    Route::group(['prefix' => 'directors', 'middleware' => ['is_director']], function () {
        Route::get('/home', 'HomeController@home')->name('directors.home');
        require (__DIR__ . '/app/directors.php');
    });
});

//rutas para especiales solo para academicos
Route::group(['prefix' => 'app', 'middleware' => ['auth'], 'namespace' => 'Academico'], function () {
    Route::group(['prefix' => 'academicos', 'middleware' => ['is_academico']], function () {
        Route::get('/home', 'HomeController@home')->name('academicos.home');
        require (__DIR__ . '/app/academicos.php');
    });
});

//rutas para especiales solo para permissions
Route::group(['prefix' => 'app', 'middleware' => ['auth'], 'namespace' => 'Permission'], function () {
    Route::group(['prefix' => 'permissions', 'middleware' => ['is_evaluacion']], function () {
        Route::get('/home', 'HomeController@home')->name('permissions.home');
        require (__DIR__ . '/app/permissions.php');
    });
});

//rutas para especiales solo para evaluacions
Route::group(['prefix' => 'app', 'middleware' => ['auth'], 'namespace' => 'Evaluacion'], function () {
    Route::group(['prefix' => 'evaluacions', 'middleware' => ['is_evaluacion']], function () {
        Route::get('/home', 'HomeController@home')->name('evaluacions.home');
        require (__DIR__ . '/app/evaluacions.php');
    });
});

// rutas para especiales solo para proyectos
Route::group(['prefix' => 'app', 'middleware' => ['auth'], 'namespace' => 'Proyecto'], function () {
    Route::group(['prefix' => 'proyectos', 'middleware' => ['is_proyecto']], function () {
        Route::get('/home', 'HomeController@home')->name('proyectos.home');
        require (__DIR__ . '/app/proyectos.php');
    });
});

// rutas para especiales solo para jefe de área de conocimiento
Route::group(['prefix' => 'app', 'middleware' => ['auth'], 'namespace' => 'Leader'], function () {
    Route::group(['prefix' => 'leaders', 'middleware' => ['is_leader']], function () {
        Route::get('/home', 'HomeController@home')->name('leaders.home');
        require (__DIR__ . '/app/leaders.php');
    });
});

//rutas para especiales solo para representants
Route::group(['prefix' => 'app', 'middleware' => ['auth'], 'namespace' => 'Representant'], function () {

    Route::group(['prefix' => 'representants', 'middleware' => ['is_representant']], function () {
        Route::get('/home', 'HomeController@home')->name('representants.home');
        require (__DIR__ . '/app/representants.php');
    });

    Route::get('/users', 'HomeController@users')->name('representants.users.index');
});

//rutas para especiales solo para estudiants
Route::group(['prefix' => 'app', 'middleware' => ['auth'], 'namespace' => 'Estudiant'], function () {
    Route::group(['prefix' => 'estudiants', 'middleware' => ['is_estudiant']], function () {
        Route::get('/home', 'HomeController@home')->name('estudiants.home');
        require (__DIR__ . '/app/estudiants.php');
    });
});

//rutas para especiales solo para admin
Route::group(['prefix' => 'admin', 'middleware' => ['auth', 'is_admin'], 'namespace' => 'Admin'], function () {
    require (__DIR__ . '/admin/main.php');
    Route::get('/users/cruda', function () {
        return view('admin.users.cruda');
    })->name('users.cruda');
});

Route::group(['middleware' => ['auth', 'is_admin']], function () {
    //fix DB
    Route::get('/include_cuentaxpagar_status_bad', [HomeController::class, 'includeCuentaxpagarStatusBad']);
    /*fix registro de pago con pagado cero*/
    Route::get('/ingreso_fraccions', 'Admin\Fix\DB\HomeController@ingreso_fraccions');
    Route::get('/fill_saldo', 'Admin\Fix\DB\HomeController@fillDAA');
    Route::get('/fill_saldo_est', 'Admin\Fix\DB\HomeController@fillDAAEstudiant');
    Route::get('/fill_caf', 'Admin\Fix\DB\HomeController@fillCAF');
    Route::get('/fill_abono', 'Admin\Fix\DB\HomeController@fillAbono');
    Route::get('/fix_daa', 'Admin\Fix\DB\HomeController@fixDAA');
    Route::get('/create_users_profesors', 'Admin\Fix\DB\HomeController@create_users_profesors');
    Route::get('/fix_pass_users_profesors', 'Admin\Fix\DB\HomeController@fix_pass_users_profesors');
    Route::get('/fix_registro_pago_combinados', 'Admin\Fix\DB\HomeController@fix_registro_pago_combinados');
    Route::get('/fix_registro_pago_combinados_2', 'Admin\Fix\DB\HomeController@fix_registro_pago_combinados_2');
    Route::get('/netear_caf_abn', 'Admin\Fix\DB\HomeController@netear_caf_abn');
    Route::get('/fix_state_active_profesors', 'Admin\Fix\DB\HomeController@fix_state_active_profesors');
    //19-02-2020
    Route::get('/fix_inscripcions_not_estudiant', 'Admin\Fix\DB\HomeController@fix_inscripcions_not_estudiant');
    Route::get('/fix_hnotas_add_hn_id', 'Admin\Fix\DB\HomeController@fix_hnotas_add_hn_id');

    /* registros aleatorios, estudiantes, representante, profesores */
    Route::get('/db_random', 'Admin\Fix\DB\HomeController@db_random');
    Route::get('/db_random_boletin', 'Admin\Fix\DB\HomeController@db_random_boletin');

    /* profesores borrados */
    Route::get('/fix_profesor_delete', 'Admin\Fix\DB\HomeController@fix_profesor_delete');

    /*fix registro de pago con pagado cero*/
    Route::get('/fix_paid_zero', 'Admin\Fix\DB\HomeController@fix_paid_zero');

    /*fix registro de pago combinado*/
    Route::get('/fix_pago_combinado_null', 'Admin\Fix\DB\HomeController@fix_pago_combinado_null');

    //AJAX
    Route::get('/api_list_registro_pagos/{start}/{size}', 'Admin\Fix\DB\Api\ApiFixRegistroPago@fix_registro_pagos')->name('api_fix_registro_pagos');
    Route::get('/fix_fill_recursos/{start}/{size}', 'Admin\Fix\DB\Api\ApiFixRegistroPago@fix_fill_recursos')->name('fix_fill_recursos');
    Route::get('/fix_status_pay/{start}/{size}', 'Admin\Fix\DB\Api\ApiFixRegistroPago@fix_status_pay')->name('fix_status_pay');

    //fix jobsQueue
    Route::get('/fix_jobs_queue_msn', 'Admin\Fix\DB\HomeController@fixJobsQueueMsn');

    Route::get('/fill_enrollments', 'Admin\Fix\DB\HomeController@fill_enrollments');

    Route::get('/fix_pago_combinado_auto', 'Admin\Fix\DB\HomeController@fix_pago_combinado_auto');

    Route::get('/fill_enrollments_estudiant_id', 'Admin\Fix\DB\HomeController@fill_enrollments_estudiant_id');
    Route::get('/fill_student_record', 'Admin\Fix\DB\HomeController@fill_student_record');

    Route::get('/fill_estudiants_enrollments', 'Admin\Fix\DB\HomeController@fill_estudiants_enrollments');

    Route::get('/toadmit_regular', 'Admin\Fix\DB\HomeController@toadmit_regular');
    Route::get('/catchments/fill', 'Admin\Fix\DB\HomeController@catchments_fill');
    Route::get('/catchments/fix', 'Admin\Fix\DB\HomeController@importCatchment');
    // Route::get('/catchments/fix', 'Admin\FixDB\CatchmentFixDB@importCatchment');

    Route::get('/update/fill/phone/representant', 'Admin\Fix\DB\HomeController@update_fill_phone_representant');

    // Activity Logs Routes
    Route::get('activity-logs', [ActivityLogController::class, 'index'])->name('activity-logs.index');
    Route::get('activity-logs/{activityLog}', [ActivityLogController::class, 'show'])->name('activity-logs.show');
});

//FixDB
Route::group(['prefix' => 'admin', 'middleware' => ['auth', 'is_admin']], function () {

    Route::get('logs', '\Rap2hpoutre\LaravelLogViewer\LogViewerController@index');

    require (__DIR__ . '/admin/fixDB/randonDB.php');
    require (__DIR__ . '/admin/fixDB/estudiants.php');
    require (__DIR__ . '/admin/fixDB/representants.php');
    require (__DIR__ . '/admin/fixDB/profesors.php');
    require (__DIR__ . '/admin/fixDB/exchanges.php');
    require (__DIR__ . '/admin/fixDB/users.php');
    require (__DIR__ . '/admin/fixDB/ingresos.php');
    require (__DIR__ . '/admin/fixDB/pevaluacions.php');
    require (__DIR__ . '/admin/fixDB/boletins.php');
    require (__DIR__ . '/admin/fixDB/catchments.php');
    require (__DIR__ . '/admin/fixDB/prosecucions.php');
});

Route::get('auth/{provider}', 'Auth\LoginController@redirectToProvider')->name('social.auth');
Route::get('auth/{provider}/callback', 'Auth\LoginController@handleProviderCallback');

// });
