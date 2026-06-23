<?php

use App\Http\Controllers\EnvioInformesNotasController;

// INI Boletins

$schedule->call(function () {
    $controller                = new EnvioInformesNotasController();
    $lapso_id                  = 2;                                                            // Cambia 2 por el lapso_id que corresponda
    $peducativo_id             = 2;                                                            // Inicial = 1, Primaria = 2, Media General = 3
    $sendWhatsappNotifications = false;                                                        // Activar notificaciones por WhatsApp (no esta lista toda la logica)
    $controller->sendBoletinesPorEmail($lapso_id, $peducativo_id, $sendWhatsappNotifications); // Cambia 2 por el lapso_id que corresponda
})->dailyAt('10:00')->skip(function () {

    return now()->toDateString() !== '2026-04-20'; // Fecha exacta del envío

    //return now()->toDateString() !== now()->toDateString(); // Esto ejecutará HOY

});

// FIN Boletins
