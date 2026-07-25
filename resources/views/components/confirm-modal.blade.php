{{--
╔══════════════════════════════════════════════════════════════╗
║  CONFIRM MODAL                                              ║
║  Modal de confirmación reutilizable con Alpine.js.          ║
║  Se comunica por eventos del navegador ($dispatch).         ║
║  Compatible con Livewire ($wire.call) o eventos propios.    ║
║                                                             ║
║  ─── USO BÁSICO ─────────────────────────────────────────── ║
║                                                             ║
║  @verbatim                                                  ║
║  {{-- En la vista (una vez por página) --}}                 ║
║  <x-confirm-modal                                           ║
║      name="delete-user"                                     ║
║      title="Eliminar usuario"                               ║
║      message="Esta acción no se puede deshacer."            ║
║      confirm-text="Sí, eliminar"                            ║
║      type="danger"                                          ║
║      action="deleteUser"                                    ║
║  />                                                         ║
║                                                             ║
║  {{-- Botón trigger (en cualquier lugar de la página) --}}  ║
║  <button                                                     ║
║      @click="$dispatch('confirm-delete-user', {             ║
║          id: {{ $user->id }},                               ║
║          message: '¿Eliminar a «{{ $user->name }}»?'        ║
║      })"                                                     ║
║  >                                                          ║
║      Eliminar                                               ║
║  </button>                                                  ║
║  @endverbatim                                               ║
║                                                             ║
║  ─── PARÁMETROS ─────────────────────────────────────────── ║
║                                                             ║
║  @param string  $name        Identificador único del modal  ║
║                              (default: 'confirm').          ║
║                              Genera el evento:              ║
║                                confirm-{name}               ║
║                                                             ║
║  @param string  $title       Título del modal               ║
║                              (default: '¿Estás seguro?')    ║
║                                                             ║
║  @param string  $message     Mensaje por defecto.           ║
║                              Se sobrescribe via             ║
║                              $event.detail.message          ║
║                              (default: 'Esta acción no se   ║
║                               puede deshacer.')             ║
║                                                             ║
║  @param string  $confirmText Texto del botón confirmar      ║
║                              (default: 'Confirmar')         ║
║                                                             ║
║  @param string  $cancelText  Texto del botón cancelar       ║
║                              (default: 'Cancelar')          ║
║                                                             ║
║  @param string  $type        Esquema de color:              ║
║                              'danger' (rojo) — eliminar     ║
║                              'warning' (ámbar) — suspender   ║
║                              'info' (azul) — información    ║
║                              (default: 'danger')            ║
║                                                             ║
║  @param string|null $action  Método de Livewire a invocar   ║
║                              al confirmar.                  ║
║                              Recibe el $event.detail.id     ║
║                              como primer argumento.         ║
║                              Si es null, dispara el evento  ║
║                              {name}-confirmed con {id}.     ║
║                              (default: null)                ║
║                                                             ║
║  ─── EVENTO DEL TRIGGER ─────────────────────────────────── ║
║                                                             ║
║  $dispatch('confirm-{name}', {                              ║
║      id: <mixed>,        // Requerido. Se pasa a $wire o    ║
║                          // al evento -confirmed.           ║
║      message: <string>,  // Opcional. Sobrescribe el        ║
║                          // mensaje por defecto.            ║
║      action: <string>,   // Opcional. Sobrescribe el método ║
║                          // Livewire del prop $action.      ║
║  })                                                         ║
║                                                             ║
║  ─── SIN LIVEwire ───────────────────────────────────────── ║
║                                                             ║
║  Si no se pasa $action, al confirmar se dispara:            ║
║    $dispatch('{name}-confirmed', { id: itemId })            ║
║  Escúchalo con:                                             ║
║    <div @delete-user-confirmed.window="...">                ║
║                                                             ║
║  ─── VARIOS MODALES EN UNA MISMA PÁGINA ─────────────────── ║
║                                                             ║
║  Cada uno debe tener un $name único:                        ║
║    <x-confirm-modal name="delete-user" action="..." />      ║
║    <x-confirm-modal name="suspend-user" action="..." />     ║
║    <x-confirm-modal name="archive-item" action="..." />     ║
╚══════════════════════════════════════════════════════════════╝
--}}
@props([
    'name' => 'confirm',
    'title' => '¿Estás seguro?',
    'message' => 'Esta acción no se puede deshacer.',
    'confirmText' => 'Confirmar',
    'cancelText' => 'Cancelar',
    'type' => 'danger',
    'action' => null,
])

@php
$event = 'confirm-' . $name;
$colors = match ($type) {
    'warning' => [
        'gradient' => 'from-amber-500 to-yellow-500',
        'bg' => 'bg-amber-100 dark:bg-amber-500/10',
        'icon' => 'text-amber-600 dark:text-amber-400',
        'btn' => 'from-amber-600 to-yellow-600 hover:from-amber-700 hover:to-yellow-700 shadow-amber-500/20',
    ],
    'info' => [
        'gradient' => 'from-blue-500 to-cyan-500',
        'bg' => 'bg-blue-100 dark:bg-blue-500/10',
        'icon' => 'text-blue-600 dark:text-blue-400',
        'btn' => 'from-blue-600 to-cyan-600 hover:from-blue-700 hover:to-cyan-700 shadow-blue-500/20',
    ],
    default => [
        'gradient' => 'from-red-500 to-rose-500',
        'bg' => 'bg-red-100 dark:bg-red-500/10',
        'icon' => 'text-red-600 dark:text-red-400',
        'btn' => 'from-red-600 to-rose-600 hover:from-red-700 hover:to-rose-700 shadow-red-500/20',
    ],
};
@endphp

<div
    x-data="{
        show: false,
        itemId: null,
        msg: '{{ $message }}',
        actionName: '{{ $action }}',
    }"
    x-on:{{ $event }}.window="
        show = true;
        itemId = $event.detail.id ?? null;
        msg = $event.detail.message || '{{ $message }}';
        if ($event.detail.action) actionName = $event.detail.action;
    "
>
    <template x-teleport="body">
        <div x-show="show" x-cloak
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed inset-0 z-[9999] flex items-center justify-center p-4"
            @keydown.escape.window="show = false">

            {{-- Backdrop --}}
            <div class="absolute inset-0 bg-black/60 backdrop-blur-sm"
                @click="show = false">
            </div>

            {{-- Card --}}
            <div class="relative w-full max-w-sm bg-white dark:bg-gray-900 rounded-2xl shadow-2xl border border-gray-200 dark:border-white/5 overflow-hidden"
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 scale-95 translate-y-4"
                x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                x-transition:leave-end="opacity-0 scale-95 translate-y-4"
                @click.outside="show = false">

                {{-- Accent bar --}}
                <div class="h-1.5 bg-gradient-to-r {{ $colors['gradient'] }}"></div>

                {{-- Body --}}
                <div class="p-6 text-center">
                    {{-- Icon --}}
                    <div class="mx-auto w-14 h-14 flex items-center justify-center rounded-full {{ $colors['bg'] }} mb-4">
                        <svg class="w-7 h-7 {{ $colors['icon'] }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                        </svg>
                    </div>

                    {{-- Title --}}
                    <h3 class="text-base font-bold text-gray-900 dark:text-white mb-2" x-text="'{{ $title }}'"></h3>

                    {{-- Message --}}
                    <p class="text-sm text-gray-500 dark:text-gray-400" x-text="msg"></p>
                </div>

                {{-- Footer --}}
                <div class="flex items-center justify-end gap-3 px-6 pb-6">
                    <button type="button" @click="show = false"
                        class="px-5 py-2.5 text-xs font-bold uppercase tracking-widest text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white bg-gray-100 dark:bg-white/5 hover:bg-gray-200 dark:hover:bg-white/10 rounded-lg border border-gray-200 dark:border-white/5 transition-all">
                        {{ $cancelText }}
                    </button>
                    <button type="button"
                        @click="
                            show = false;
                            @if($action)
                                if (itemId !== null) { $wire.call(actionName, itemId); }
                                else { $wire.call(actionName); }
                            @else
                                $dispatch('{{ $name }}-confirmed', { id: itemId });
                            @endif
                        "
                        class="inline-flex items-center gap-2 px-5 py-2.5 text-xs font-bold uppercase tracking-widest text-white bg-gradient-to-r {{ $colors['btn'] }} rounded-lg shadow-lg transition-all">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        {{ $confirmText }}
                    </button>
                </div>
            </div>
        </div>
    </template>
</div>
