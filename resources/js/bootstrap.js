/**
 * We'll load the axios HTTP library which allows us to easily issue requests
 * to our Laravel back-end. This library automatically handles sending the
 * CSRF token as a header based on the value of the "XSRF" token cookie.
 */

import axios from 'axios';
window.axios = axios;

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

/**
 * Laravel Echo — configurado para Laravel Reverb (WebSockets).
 * Solo se inicializa si la página incluye el atributo data-reverb="enabled"
 * en la etiqueta <html>. Las páginas que necesitan WebSockets en tiempo real
 * (scoreboards de competencias, etc.) deben agregar este atributo.
 */

import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

window.Pusher = Pusher;

if (document.documentElement.dataset.reverb === 'enabled') {
    window.Echo = new Echo({
        broadcaster: 'reverb',
        key: import.meta.env.VITE_REVERB_APP_KEY,
        wsHost: import.meta.env.VITE_REVERB_HOST,
        wsPort: import.meta.env.VITE_REVERB_PORT ?? 8080,
        wssPort: import.meta.env.VITE_REVERB_PORT ?? 8080,
        forceTLS: (import.meta.env.VITE_REVERB_SCHEME ?? 'http') === 'https',
        enabledTransports: ['ws', 'wss'],
        disableStats: true,
    });

    // Evento "lesson.scheduled" (canal privado App.Models.User.{id}).
    // Al recibirlo, notifica a los componentes Livewire que escuchan
    // 'lesson-scheduled' (p. ej. el contador de lecciones programadas).
    // Pasa el payload completo para que el componente pueda mostrar toast.
    const userId = document.documentElement.dataset.userId;
    if (userId) {
        window.Echo.private(`App.Models.User.${userId}`)
            .listen('.lesson.scheduled', (e) => {
                Livewire.dispatch('lesson-scheduled', e);

                // ACK (Opción 10): confirma la entrega al backend para la
                // auditoría broadcast_events.delivered. Idempotente y
                // rate-limited en el servidor.
                if (e?.event_id) {
                    axios.post('/api/broadcast/ack', { event_id: e.event_id })
                        .catch(() => {
                            // Silencioso: el ACK es best-effort para métricas.
                        });
                }
            });
    }

    /*
     * Canal de PRESENCIA `presence-app.sessions`.
     *
     * Reverb mantiene la lista de miembros y empuja `member_added` /
     * `member_removed` al instante, así que el dashboard /admin puede seguir
     * los usuarios conectados en tiempo real sin polling ni scheduler.
     *
     * Se une UNA sola vez y se expone en `window.sessionsPresence`: si dos
     * partes de la página llamaran a `Echo.join()` por su cuenta, la misma
     * persona se contaría dos veces (una por pestaña).
     */
    window.sessionsPresence = window.Echo.join('app.sessions');
}

