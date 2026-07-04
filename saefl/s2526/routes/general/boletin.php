<?php

use App\Http\Controllers\WhatsAppTemplateSendInformeNotasController;
use App\Http\Controllers\BoletinController;

// Route::get('/boletins', [EnvioInformesNotasController::class, 'sendBoletinesPorEmail']);

// Route::get('/general/boletin/{token}', [WhatsAppTemplateSendInformeNotasController::class, 'sendInformeNotas']);

Route::get('/general/send/boletin/{token}', [WhatsAppTemplateSendInformeNotasController::class, 'sendInformeNotas']); // envia a meta el messege
Route::get('/general/boletin/{token}', [BoletinController::class, 'showBoletinByToken'])->name('send.informe.notas.token'); // muestra el pdf

Route::get('/informe/notas/{token?}', [BoletinController::class, 'showBoletinByToken'])->name('informe.notas.token');

?>
