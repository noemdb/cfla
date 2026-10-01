<?php

use Illuminate\Support\Facades\Broadcast;

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Here you may register all of the event broadcasting channels that your
| application supports. The given channel authorization callbacks are
| used to check if an authenticated user can listen to the channel.
|
*/

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

// Chart "sesiones activas" del dashboard /admin: solo admin o
// personal de diagnóstico (evento ActiveSessionsUpdated).
Broadcast::channel('admin.sessions', function ($user) {
    return $user->isAdminOrDiagnostic();
});

/*
|--------------------------------------------------------------------------
| Canal de PRESENCIA `presence-app.sessions`
|--------------------------------------------------------------------------
| Devolver un ARRAY (no `true`) convierte el canal en de presencia: Reverb
| lleva la cuenta de miembros y empuja `member_added` / `member_removed` al
| instante a todos los suscritos. Es lo que permite que el dashboard /admin
| siga los usuarios conectados EN TIEMPO REAL, sin polling ni scheduler (el
| canal privado `admin.sessions` solo empujaba un valor por minuto desde
| `admin:broadcast-sessions`, que depende del cron).
|
| Privacidad: los datos de cada miembro los elige el servidor al autorizar.
| Solo los roles privilegiados publican nombre y rol; al resto se les publica
| únicamente su id, así un alumno conectado no puede ver la lista de cuentas
| del personal que está en línea.
*/
Broadcast::channel('app.sessions', function ($user) {
    // Usuarios dados de baja no entran al conteo (tampoco pueden iniciar sesión).
    if (($user->is_active ?? 'enable') !== 'enable') {
        return false;
    }

    $member = ['id' => (int) $user->id];

    $privileged = $user->isAdminOrDiagnostic()
        || $user->is_planner
        || $user->is_coordinacion
        || $user->is_leadership
        || $user->is_director
        || $user->is_profesor;

    if ($privileged) {
        // `loadMissing` evita el query extra del accessor full_name.
        $user->loadMissing('profile');

        $member += [
            'username' => $user->username,
            'fullname' => $user->profile
                ? trim($user->profile->firstname.' '.$user->profile->lastname)
                : $user->username,
            'role' => $user->role_label,
        ];
    }

    return $member;
});
