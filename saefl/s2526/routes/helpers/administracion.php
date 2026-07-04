<?php

use App\Http\Controllers\Helper\ManulasController;

// Rutas para partials del modal de navegación
Route::group(['prefix' => 'app', 'middleware' => ['auth'], 'namespace' => 'Helper'], function () {
    Route::get('/asistente_registro_pago', [ManulasController::class, 'asistenteRegistroPago'])->name('helpers.asistenteRegistroPago');
    Route::get('/cuentaxpagars_list', [ManulasController::class, 'cuentaxpagars_list'])->name('helpers.cuentaxpagarsList');
    Route::get('/asistente_individual', [ManulasController::class, 'asistenteIndividual'])->name('helpers.asistenteIndividual');
    Route::get('/cancelations', [ManulasController::class, 'cancelations'])->name('helpers.cancelations');
    Route::get('/cuentasxpagars', [ManulasController::class, 'cuentasxpagars'])->name('helpers.cuentasxpagars');
    Route::get('/estrutura-cobranzas', [ManulasController::class, 'estruturaCobranzas'])->name('helpers.estruturaCobranzas');
    Route::get('/gestion-rols', [ManulasController::class, 'gestionRols'])->name('helpers.gestionRols');
    Route::get('/manage-worker', [ManulasController::class, 'manageWorker'])->name('helpers.manageWorker');
    Route::get('/morosidad', [ManulasController::class, 'morosidad'])->name('helpers.morosidad');
    Route::get('/restablecimiento-bio', [ManulasController::class, 'restablecimientoBIO'])->name('helpers.restablecimientoBIO');
    Route::get('/liberaciones', [ManulasController::class, 'liberaciones'])->name('helpers.liberaciones');
    Route::get('/conceptopagos', [ManulasController::class, 'conceptopagos'])->name('helpers.conceptopagos');
    Route::get('/retiros', [ManulasController::class, 'retiros'])->name('helpers.retiros');
    Route::get('/representants/saldos', [ManulasController::class, 'representantsSaldos'])->name('helpers.representants.saldos');
    Route::get('/representants/saldos/date', function () {
        return view('administracion.instructions.representantsSaldosDate');
    })->name('helpers.representants.saldosDate');
    Route::get('/bancos/libros', [ManulasController::class, 'bancosLibros'])->name('helpers.bancos.libros');
    Route::get('/representants/historico', [ManulasController::class, 'representantsHistorico'])->name('helpers.representants.historico');
    Route::get('/retiros/pronto/pago', [ManulasController::class, 'retirosProntoPago'])->name('helpers.retiros.pronto.pago');
    Route::get('/registropagos/listado', [ManulasController::class, 'registropagosListado'])->name('helpers.registropagos.listado');
    Route::get('/ingresos/listado', [ManulasController::class, 'ingresosListado'])->name('helpers.ingresos.listado');
    Route::get('/payments/listado', [ManulasController::class, 'paymentsListado'])->name('helpers.payments.listado');
    Route::get('/send/notifications/collection', [ManulasController::class, 'wizardAdministrativas'])->name('helpers.wizard.administrativas');

    Route::get('/wizard/administrativas', [ManulasController::class, 'sendNotificationsCollection'])->name('helpers.send.notifications.collection');

    Route::get('/representants/blacklist/manage', [ManulasController::class, 'representantsBlacklistManage'])->name('helpers.representants.blacklist.manage');

    Route::get('/calendarevents/manage', [ManulasController::class, 'CalendarEventManage'])->name('helpers.CalendarEvent.Manage');
    Route::get('/boletin/revision', [ManulasController::class, 'boletinRevision'])->name('helpers.boletinRevision');

    // Indicadores Administrativos (rol: admon)
    Route::get('instructions/indicators/admon', function () {
        return view('administracion.instructions.indicatorsAdmon');
    })->name('helpers.indicatorsAdmon');

    // Indicadores Académicos (rol: control)
    Route::get('instructions/indicators/control', function () {
        return view('administracion.instructions.indicatorsControl');
    })->name('helpers.indicatorsControl');
});

// Ruta dinámica para cualquier partial (opcional)
Route::get('/load-partial/{partial}', [ManulasController::class, 'loadPartial'])->name('partials.load');

/////////////////////////////////////////////////////////////////////////////////////////
//printable (PDF)
Route::get('instructions/pdf/cuentaxpagarsList', function () {
    return view('administracion.instructions.pdf.cuentaxpagarsList');
})->name('helpers.pdf.cuentaxpagarsList');

Route::get('instructions/pdf/retiros', function () {
    return view('administracion.instructions.pdf.retiros');
})->name('helpers.pdf.retiros');

Route::get('instructions/pdf/cancelations', function () {
    return view('administracion.instructions.pdf.cancelations');
})->name('helpers.pdf.cancelations');

Route::get('instructions/pdf/conceptopagos', function () {
    return view('administracion.instructions.pdf.conceptopagos');
})->name('helpers.pdf.conceptopagos');

Route::get('instructions/pdf/cuentasxpagars', function () {
    return view('administracion.instructions.pdf.cuentasxpagars');
})->name('helpers.pdf.cuentasxpagars');

Route::get('instructions/pdf/estruturaCobranzas', function () {
    return view('administracion.instructions.pdf.estruturaCobranzas');
})->name('helpers.pdf.estruturaCobranzas');

Route::get('instructions/pdf/gestionRols', function () {
    return view('administracion.instructions.pdf.gestionRols');
})->name('helpers.pdf.gestionRols');

Route::get('instructions/pdf/liberaciones', function () {
    return view('administracion.instructions.pdf.liberaciones');
})->name('helpers.pdf.liberaciones');

Route::get('instructions/pdf/manageWorker', function () {
    return view('administracion.instructions.pdf.manageWorker');
})->name('helpers.pdf.manageWorker');

Route::get('instructions/pdf/morosidad', function () {
    return view('administracion.instructions.pdf.morosidad');
})->name('helpers.pdf.morosidad');

Route::get('instructions/pdf/restablecimientoBIO', function () {
    return view('administracion.instructions.pdf.restablecimientoBIO');
})->name('helpers.pdf.restablecimientoBIO');

Route::get('instructions/pdf/asistenteRegistroPago', function () {
    return view('administracion.instructions.pdf.asistenteRegistroPago');
})->name('helpers.pdf.asistenteRegistroPago');

Route::get('instructions/pdf/representantsSaldos', function () {
    return view('administracion.instructions.pdf.representantsSaldos');
})->name('helpers.pdf.representants.saldos');

Route::get('instructions/pdf/bancos/facturacion', function () {
    return view('administracion.instructions.pdf.bancosLibros');
})->name('helpers.pdf.bancos.facturacion');


Route::get('instructions/pdf/bancos/facturacion', function () {
    return view('administracion.instructions.pdf.bancosLibros');
})->name('helpers.pdf.bancos.facturacion');


Route::get('instructions/pdf/representants/historico', function () {
    return view('administracion.instructions.pdf.representantsHistorico');
})->name('helpers.pdf.representants.historico');

Route::get('instructions/pdf/retiros/pronto/pago', function () {
    return view('administracion.instructions.pdf.retirosProntoPago');
})->name('helpers.pdf.retiros.pronto.pago');

Route::get('instructions/pdf/registropagos/listado', function () {
    return view('administracion.instructions.pdf.registropagosListado');
})->name('helpers.pdf.listadoReportesPago');

Route::get('instructions/pdf/ingresos/listado', function () {
    return view('administracion.instructions.pdf.ingresosListado');
})->name('helpers.pdf.ingresos.listado');

Route::get('instructions/pdf/payments/listado', function () {
    return view('administracion.instructions.pdf.paymentsListado');
})->name('helpers.pdf.payments.listado');


Route::get('instructions/pdf/wizard/administrativas', function () {
    return view('administracion.instructions.pdf.wizardAdministrativas');
})->name('helpers.pdf.wizard.administrativas');


Route::get('instructions/pdf/wizard/administrativas', function () {
    return view('administracion.instructions.pdf.sendNotificationsCollection');
})->name('helpers.pdf.send.notifications.collection');
