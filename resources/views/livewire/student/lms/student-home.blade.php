@php
// D2 · Color por materia. Paleta literal con clases Tailwind: el JIT escanea
// .blade.php bajo resources/ (no app/), así que las clases concretas viven aquí
// y la lógica de asignación en Asignatura::colorKey(). Misma clave → mismo
// color en claro y oscuro, en todas las vistas del LMS.
$__sc = [
    'sky' => [
        'dot'      => 'bg-sky-400',
        'badge'    => 'bg-sky-100 text-sky-700 dark:bg-sky-500/10 dark:text-sky-300 border border-sky-300 dark:border-sky-500/30',
        'chip'     => 'bg-sky-500/10 text-sky-400',
        'text'     => 'text-sky-600 dark:text-sky-300',
        'gradient' => 'linear-gradient(90deg, #0ea5e9, #38bdf8)',
    ],
    'emerald' => [
        'dot'      => 'bg-emerald-400',
        'badge'    => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-500/10 dark:text-emerald-300 border border-emerald-300 dark:border-emerald-500/30',
        'chip'     => 'bg-emerald-500/10 text-emerald-400',
        'text'     => 'text-emerald-600 dark:text-emerald-300',
        'gradient' => 'linear-gradient(90deg, #10b981, #34d399)',
    ],
    'amber' => [
        'dot'      => 'bg-amber-400',
        'badge'    => 'bg-amber-100 text-amber-700 dark:bg-amber-500/10 dark:text-amber-300 border border-amber-300 dark:border-amber-500/30',
        'chip'     => 'bg-amber-500/10 text-amber-400',
        'text'     => 'text-amber-600 dark:text-amber-300',
        'gradient' => 'linear-gradient(90deg, #f59e0b, #fbbf24)',
    ],
    'indigo' => [
        'dot'      => 'bg-indigo-400',
        'badge'    => 'bg-indigo-100 text-indigo-700 dark:bg-indigo-500/10 dark:text-indigo-300 border border-indigo-300 dark:border-indigo-500/30',
        'chip'     => 'bg-indigo-500/10 text-indigo-400',
        'text'     => 'text-indigo-600 dark:text-indigo-300',
        'gradient' => 'linear-gradient(90deg, #6366f1, #818cf8)',
    ],
    'purple' => [
        'dot'      => 'bg-purple-400',
        'badge'    => 'bg-purple-100 text-purple-700 dark:bg-purple-500/10 dark:text-purple-300 border border-purple-300 dark:border-purple-500/30',
        'chip'     => 'bg-purple-500/10 text-purple-400',
        'text'     => 'text-purple-600 dark:text-purple-300',
        'gradient' => 'linear-gradient(90deg, #a855f7, #c084fc)',
    ],
    'orange' => [
        'dot'      => 'bg-orange-400',
        'badge'    => 'bg-orange-100 text-orange-700 dark:bg-orange-500/10 dark:text-orange-300 border border-orange-300 dark:border-orange-500/30',
        'chip'     => 'bg-orange-500/10 text-orange-400',
        'text'     => 'text-orange-600 dark:text-orange-300',
        'gradient' => 'linear-gradient(90deg, #f97316, #fb923c)',
    ],
    'rose' => [
        'dot'      => 'bg-rose-400',
        'badge'    => 'bg-rose-100 text-rose-700 dark:bg-rose-500/10 dark:text-rose-300 border border-rose-300 dark:border-rose-500/30',
        'chip'     => 'bg-rose-500/10 text-rose-400',
        'text'     => 'text-rose-600 dark:text-rose-300',
        'gradient' => 'linear-gradient(90deg, #f43f5e, #fb7185)',
    ],
    'teal' => [
        'dot'      => 'bg-teal-400',
        'badge'    => 'bg-teal-100 text-teal-700 dark:bg-teal-500/10 dark:text-teal-300 border border-teal-300 dark:border-teal-500/30',
        'chip'     => 'bg-teal-500/10 text-teal-400',
        'text'     => 'text-teal-600 dark:text-teal-300',
        'gradient' => 'linear-gradient(90deg, #14b8a6, #2dd4bf)',
    ],
    'slate' => [
        'dot'      => 'bg-slate-400',
        'badge'    => 'bg-slate-100 text-slate-700 dark:bg-slate-500/10 dark:text-slate-300 border border-slate-300 dark:border-slate-500/30',
        'chip'     => 'bg-slate-500/10 text-slate-400',
        'text'     => 'text-slate-600 dark:text-slate-300',
        'gradient' => 'linear-gradient(90deg, #64748b, #94a3b8)',
    ],
];
$__scKey = static fn (?string $name): string => \App\Models\app\Academy\Asignatura::colorKey($name);
@endphp

@php
    $tabs = ['continuar', 'lecciones', 'distribucion', 'actividad'];
@endphp

{{-- D3: estado Alpine de la mini-barra sticky. updateNext() mide el fin del hero
     (x-ref="heroSection") y muestra la barra sólo cuando el hero — y su CTA de
     próxima lección — queda fuera de pantalla (detrás del navbar de h-14 = 56px).
     @scroll.window.passive es declarativo: no re-registra listeners en cada morph
     del wire:poll.10s. --}}
<div class="max-w-4xl mx-auto py-8 px-4 space-y-8"
     wire:poll.10s
     x-data="{
        nextOpen: false,
        updateNext() {
            const hero = this.$refs.heroSection;
            this.nextOpen = !!hero && hero.getBoundingClientRect().bottom <= 56;
        },
        activeTab: @entomb('{{ $activeTab }}'),
        tabs: ['continuar', 'lecciones', 'distribucion', 'actividad'],
        setActiveTab(tab) {
            this.activeTab = tab;
            @this.setActiveTab(tab)
        },
        prevTab() {
            const index = this.tabs.indexOf(this.activeTab);
            const newIndex = (index - 1 + this.tabs.length) % this.tabs.length;
            this.setActiveTab(this.tabs[newIndex]);
        },
        nextTab() {
            const index = this.tabs.indexOf(this.activeTab);
            const newIndex = (index + 1) % this.tabs.length;
            this.setActiveTab(this.tabs[newIndex]);
        },
        init() {
            this.activeTab = @entomb('{{ $activeTab }}');
        }
     }"
     @scroll.window.passive="updateNext()"
     x-init="updateNext(); init()">

    {{-- 0. Hero: saludo + progreso + siguiente lección.
         G1: todas las tarjetas del home comparten la misma receta de
         transición (transition-all duration-200 ease-out). --}}
    <section x-ref="heroSection" class="bg-white dark:bg-gray-800/50 border border-gray-200 dark:border-gray-700 rounded-2xl p-5 sm:p-6 shadow-sm transition-all duration-200 ease-out">
        <div class="flex flex-col sm:flex-row sm:items-center gap-6">
            <div class="flex-1 min-w-0">
                <div class="flex items-center gap-3">
                    <div class="min-w-0">
                        <p class="text-xs font-semibold uppercase tracking-wider text-emerald-600 dark:text-emerald-400">{{ $greeting }}</p>
                        <h1 class="text-2xl font-display font-bold text-gray-900 dark:text-white mt-1">{{ $firstName }}</h1>
                    </div>
                    @if($showMascot)
                    <x-lms.mascot :variant="'greet'" :size="'sm'" :emphasis="$mascotEmphasis" />
                    @endif
                </div>
                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                    Tu avance en un vistazo. Sigue aprendiendo sin perder el ritmo.
                </p>

                <div class="mt-4 flex flex-wrap items-center gap-2">
                    @if($streak > 0)
                    {{-- C2: racha en la familia del countdown del hero (ámbar) y celebración
                         con un "pop" al cargar la página (login). El SVG de fuego sustituye
                         al emoji 🔥 (los emojis se corrompen en esta base, ver C3).
                         G3: tabular-nums en el contenedor (heredado al dígito) — la cifra
                         "2 días de racha" debe quedar CONTIGUA (el test lo aserta), por eso
                         no se envuelve el número en un <span>. --}}
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold tabular-nums text-amber-700 dark:text-amber-300 bg-amber-100 dark:bg-amber-500/10 border border-amber-200 dark:border-amber-500/30 animate-streak-pop">
                        <svg class="w-3.5 h-3.5" aria-hidden="true" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M12.395 2.553a1 1 0 00-1.45-.385c-.345.23-.614.558-.822.88-.214.33-.403.713-.57 1.116-.334.804-.614 1.768-.84 2.734a31.365 31.365 0 00-.613 3.58 2.64 2.64 0 01-.945-1.067c-.328-.68-.398-1.534-.398-2.654A1 1 0 005.05 6.05c-1.354 1.553-2.4 3.307-3.018 5.194-.512 1.565-.17 3.24.89 4.303C4.087 16.616 5.857 17.34 8.004 17.34c2.148 0 3.918-.724 5.08-1.793 1.06-1.062 1.402-2.738.89-4.303-.618-1.887-1.663-3.64-3.017-5.194a1 1 0 00-1.562.112z" clip-rule="evenodd"/>
                        </svg>
                        {{ $streak }} {{ $streak === 1 ? 'día' : 'días' }} de racha
                    </span>
                    @endif
                </div>

                @if($nextLesson)
                <a href="{{ route('student.lms.activity', $nextLesson) }}"
                   class="group mt-4 inline-flex items-center gap-2 px-4 py-2.5 min-h-[44px] rounded-lg bg-emerald-600 hover:bg-emerald-500 text-white text-sm font-display font-semibold shadow-sm transition-colors focus-visible:ring-2 ring-emerald-500/50 focus-visible:ring-offset-2">
                    @if($modoLectura)
                    {{-- F3: micro-copia infantil. Para 5–8 el CTA pide una única
                         acción ("Pulsa para empezar"); el nombre de la lección
                         queda como texto de apoyo para lectores de pantalla. La
                         barra sticky D3 (más abajo) mantiene el botón "Continuar"
                         intacto. --}}
                    <span class="sr-only">{{ $nextLesson->topic }}</span>
                    <span>Pulsa para empezar</span>
                    @else
                    <span class="max-w-[16rem] sm:max-w-xs truncate">{{ $nextLesson->topic }}</span>
                    @endif
                    <svg class="w-4 h-4 shrink-0 group-hover:translate-x-0.5 transition-transform" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </a>
                @php $nextKey = $__scKey($nextLesson->pevaluacion?->pensum?->asignatura?->name); @endphp
                <p class="mt-2 flex flex-wrap items-center gap-x-2 gap-y-1 text-xs text-gray-600 dark:text-gray-400">
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold {{ $__sc[$nextKey]['badge'] }}">
                        <svg class="w-3.5 h-3.5" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        {{ $nextLesson->pevaluacion?->pensum?->asignatura?->name ?? 'Lección' }}
                    </span>
                    @if($nextLesson->lmsPublication?->publish_at?->isFuture())
                        <span class="inline-flex items-center gap-1 font-semibold text-amber-600 dark:text-amber-400"
                              x-data="{ target: '{{ $nextLesson->lmsPublication->publish_at->toIso8601String() }}', label: '', timer: null, tick() { const left = new Date(this.target) - new Date(); if (left <= 0) { this.label = 'Publicada ahora'; clearInterval(this.timer); return; } const h = Math.floor(left / 3.6e6); const m = Math.floor((left % 3.6e6) / 6e4); const s = Math.floor((left % 6e4) / 1e3); this.label = 'Comienza en ' + h + 'h ' + m + 'm ' + s + 's'; }, init() { this.tick(); this.timer = setInterval(() => this.tick(), 1000); } }"
                              x-text="label">Comienza en…</span>
                    @else
                        <span class="inline-flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5 text-emerald-500" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            {{ $nextLesson->lmsPublication?->publish_at?->diffForHumans() }}
                        </span>
                    @endif
                </p>
                @else
                <p class="mt-4 flex items-center gap-2 text-sm text-gray-600 dark:text-gray-400">
                    <svg class="w-4 h-4 shrink-0 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Aún no hay lecciones disponibles. Tus profesores publicarán contenido pronto.
                </p>
                @endif
            </div>

            <div class="shrink-0 mx-auto sm:mx-0">
                <div class="relative w-36 h-36" x-data="{ pct: 0, target: {{ $stats['progress_pct'] }} }"
                     x-init="() => { if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) { pct = target; return; } const start = performance.now(); const dur = 1000; const step = (now) => { const k = Math.min((now - start) / dur, 1); pct = Math.round(target * (1 - Math.pow(1 - k, 3))); if (k < 1) requestAnimationFrame(step); }; requestAnimationFrame(step); }">
                    <svg class="w-36 h-36 -rotate-90" viewBox="0 0 120 120" aria-hidden="true">
                        <circle cx="60" cy="60" r="52" fill="none" stroke-width="10" class="stroke-gray-100 dark:stroke-gray-700/60"></circle>
                        <circle cx="60" cy="60" r="52" fill="none" stroke-width="10" stroke-linecap="round"
                                class="stroke-emerald-500"
                                stroke-dasharray="326.7"
                                :style="'stroke-dashoffset: ' + (326.7 - (326.7 * pct / 100))"
                                style="stroke-dashoffset: 326.7; transition: stroke-dashoffset 1s cubic-bezier(0.22, 1, 0.36, 1);"></circle>
                    </svg>
                    <div class="absolute inset-0 flex flex-col items-center justify-center">
                        <span class="text-2xl font-bold text-gray-900 dark:text-white tabular-nums" x-text="pct + '%'">0%</span>
                        <span class="text-xs font-medium uppercase tracking-wider text-gray-400 dark:text-gray-500">completado</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    @if($nextLesson)
    {{-- D3: mini-barra sticky "Próxima lección". Mantiene la siguiente lección
         visible al hacer scroll, cuando el hero y su CTA ya salieron de pantalla.
         Reutiliza el color de materia (D2) para el punto y la etiqueta. La
         animación x-transition se anula sola bajo prefers-reduced-motion (bloque
         <style> al final de esta vista). sticky top-14 = justo debajo del navbar
         (h-14). Pulido: flotante full-width con vidrio — rompe fuera del
         max-w-4xl hacia el ancho real de la página (-mx-[calc((100vw-100%)/2)])
         como el navbar, con px-[calc((100vw-100%)/2)] que realinea el contenido
         al mismo eje de la columna; border-y (no border, para no dejar líneas en
         los bordes de pantalla) + backdrop-blur + shadow-lg, y hueco de vuelo
         !mt-2 que vence el margin-top del space-y-8 del contenedor. --}}
    <div x-show="nextOpen"
         x-cloak
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 -translate-y-1"
         x-transition:enter-end="opacity-100 translate-y-0"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 translate-y-0"
         x-transition:leave-end="opacity-0 -translate-y-1"
         aria-label="Próxima lección"
         class="sticky top-14 z-20 !mt-2 -mx-[calc((100vw-100%)/2)] px-[calc((100vw-100%)/2)] py-2 bg-white/80 dark:bg-gray-800/80 backdrop-blur-md border-y border-gray-200/80 dark:border-gray-700/70 shadow-lg shadow-gray-300/20 dark:shadow-black/20">
        @php $nextKey = $__scKey($nextLesson->pevaluacion?->pensum?->asignatura?->name); @endphp
        <div class="flex items-center gap-2">
            <a href="{{ route('student.lms.activity', $nextLesson) }}"
               class="flex items-center gap-2 min-w-0 flex-1 group"
               aria-label="Ir a {{ $nextLesson->topic }}">
                <span class="w-2 h-2 rounded-full {{ $__sc[$nextKey]['dot'] }} shrink-0" aria-hidden="true"></span>
                <span class="hidden sm:inline text-xs font-semibold uppercase tracking-wider {{ $__sc[$nextKey]['text'] }} shrink-0">Próxima lección</span>
                <span class="text-sm font-medium text-gray-700 dark:text-gray-200 truncate min-w-0 group-hover:text-gray-900 dark:group-hover:text-white">{{ $nextLesson->topic }}</span>
            </a>
            <a href="{{ route('student.lms.activity', $nextLesson) }}"
               class="shrink-0 inline-flex items-center gap-1 px-3 py-1.5 min-h-[36px] rounded-lg bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-semibold font-display shadow-sm transition-colors focus-visible:ring-2 ring-emerald-500/50 focus-visible:ring-offset-2">
                Continuar
                <svg class="w-3.5 h-3.5" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
            </a>
        </div>
    </div>
    @endif

    {{-- 1. Stats Cards --}}
    {{-- F2: en modo lectura (5–8) una sola barra de progreso grande y simple
         sustituye la rejilla de 4 tarjetas: menos opciones por pantalla y una
         sola señal de avance clara. Sin SVG ni emojis (se corrompen en esta
         base). Para 9–12 y 13–15 se mantiene la rejilla completa. --}}
    @if($modoLectura)
    <section aria-label="Tu progreso" class="mb-6">
        <div class="bg-white dark:bg-gray-800/50 border border-gray-200 dark:border-gray-700 rounded-xl p-5 space-y-4 shadow-sm transition-all duration-200 ease-out">
            <div class="flex items-baseline justify-between gap-4">
                <p class="text-base font-semibold text-gray-900 dark:text-white">Tu progreso</p>
                <p class="text-2xl font-bold text-emerald-600 dark:text-emerald-400 tabular-nums">{{ $stats['progress_pct'] }}%</p>
            </div>
            <div role="progressbar" aria-valuenow="{{ $stats['progress_pct'] }}" aria-valuemin="0" aria-valuemax="100"
                 aria-label="Progreso en las lecciones"
                 class="h-4 w-full rounded-full bg-gray-100 dark:bg-gray-700/60 overflow-hidden">
                <div class="h-full rounded-full transition-all duration-700"
                     style="width: {{ $stats['progress_pct'] }}%; background: linear-gradient(90deg, #10b981, #34d399);"></div>
            </div>
            <p class="text-sm text-gray-600 dark:text-gray-400">
                {{ $stats['completed'] }} de {{ $stats['total'] }} lecciones completadas
            </p>
        </div>
    </section>
    @else
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
        {{-- Lecciones --}}
        <div class="bg-white dark:bg-gray-800/50 border border-gray-200 dark:border-gray-700 rounded-xl p-4 space-y-2 shadow-sm transition-all duration-200 ease-out">
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 rounded-lg bg-sky-500/10 flex items-center justify-center">
                    <svg class="w-4 h-4 text-sky-400" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                    </svg>
                </div>
                <span class="text-xs font-semibold uppercase tracking-wider text-gray-400 dark:text-gray-500">Lecciones</span>
            </div>
            <p class="text-2xl font-bold text-gray-900 dark:text-white tabular-nums">{{ $stats['total'] }}</p>
            <p class="text-xs text-gray-600 dark:text-gray-400">Disponibles para ti</p>
        </div>

        {{-- Completadas --}}
        <div class="bg-white dark:bg-gray-800/50 border border-gray-200 dark:border-gray-700 rounded-xl p-4 space-y-2 shadow-sm transition-all duration-200 ease-out">
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 rounded-lg bg-emerald-500/10 flex items-center justify-center">
                    <svg class="w-4 h-4 text-emerald-400" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <span class="text-xs font-semibold uppercase tracking-wider text-gray-400 dark:text-gray-500">Completadas</span>
            </div>
            <p class="text-2xl font-bold text-gray-900 dark:text-white tabular-nums">{{ $stats['completed'] }}</p>
            <p class="text-xs text-gray-600 dark:text-gray-400">
                @if($stats['total'] > 0)
                    {{ $stats['progress_pct'] }}% del total
                @else
                    Sin actividades
                @endif
            </p>
        </div>

        {{-- Comentarios --}}
        <div class="bg-white dark:bg-gray-800/50 border border-gray-200 dark:border-gray-700 rounded-xl p-4 space-y-2 shadow-sm transition-all duration-200 ease-out">
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 rounded-lg bg-amber-500/10 flex items-center justify-center">
                    <svg class="w-4 h-4 text-amber-400" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/>
                    </svg>
                </div>
                <span class="text-xs font-semibold uppercase tracking-wider text-gray-400 dark:text-gray-500">Comentarios</span>
            </div>
            <p class="text-2xl font-bold text-gray-900 dark:text-white tabular-nums">{{ $stats['comments'] }}</p>
            <p class="text-xs text-gray-600 dark:text-gray-400">Que has dejado</p>
        </div>

        {{-- Descargas --}}
        <div class="bg-white dark:bg-gray-800/50 border border-gray-200 dark:border-gray-700 rounded-xl p-4 space-y-2 shadow-sm transition-all duration-200 ease-out">
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 rounded-lg bg-purple-500/10 flex items-center justify-center">
                    <svg class="w-4 h-4 text-purple-400" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                    </svg>
                </div>
                <span class="text-xs font-semibold uppercase tracking-wider text-gray-400 dark:text-gray-500">Descargas</span>
            </div>
            <p class="text-2xl font-bold text-gray-900 dark:text-white tabular-nums">{{ $stats['downloads'] }}</p>
            <p class="text-xs text-gray-600 dark:text-gray-400">Recursos descargados</p>
        </div>
    </div>
    @endif

    {{-- 2. Continue Learning --}}
    @if($recentLogs->isNotEmpty())
    <section>
        <div class="flex items-center gap-2 mb-4">
            <svg class="w-4 h-4 text-emerald-400" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
            </svg>
            <h2 class="text-base font-display font-bold text-gray-900 dark:text-white">Continuar Aprendiendo</h2>
        </div>

        <div class="space-y-2">
            @foreach($recentLogs as $log)
                @php $act = $log->activity; @endphp
                @if(!$act) @continue @endif
                @php $key = $__scKey($act->pevaluacion?->pensum?->asignatura?->name); @endphp
                <a href="{{ route('student.lms.activity', $act) }}"
                   class="group block bg-white dark:bg-gray-800/50 border border-gray-200 dark:border-gray-700 rounded-xl p-5 shadow-sm transition-all duration-200 ease-out hover:-translate-y-0.5 hover:shadow-md hover:border-emerald-500/40 motion-reduce:transform-none motion-reduce:transition-none focus-visible:ring-2 ring-emerald-500/50 focus-visible:ring-offset-2">
                    <div class="flex items-start justify-between gap-3">
                        <div class="flex items-start gap-3 min-w-0">
                            <div @class([
                                'w-9 h-9 rounded-lg flex items-center justify-center shrink-0 mt-0.5',
                                'bg-emerald-500/10' => $log->event === 'COMPLETE',
                                'bg-sky-500/10' => $log->event !== 'COMPLETE',
                            ])>
                                @if($log->event === 'COMPLETE')
                                    <svg class="w-4 h-4 text-emerald-400" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                @else
                                    <svg class="w-4 h-4 text-sky-400" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/>
                                    </svg>
                                @endif
                            </div>
                            <div class="min-w-0">
                                <div class="flex items-center gap-2">
                                    <p class="text-base font-display font-medium text-gray-900 dark:text-white group-hover:text-emerald-600 dark:group-hover:text-emerald-400 transition-colors truncate">
                                        {{ $act->topic ?? 'Actividad sin título' }}
                                    </p>
                                    @if($act->lmsPublication?->isPreviewToStudents())
                                        <span class="shrink-0 inline-flex items-center gap-1 px-1.5 py-0.5 rounded-full text-xs font-semibold uppercase tracking-wider text-amber-700 dark:text-amber-300 bg-amber-100 dark:bg-amber-500/10 border border-amber-300 dark:border-amber-500/30">
                                            <svg class="w-2.5 h-2.5" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                            </svg>
                                            Vista previa
                                        </span>
                                    @endif
                                </div>
                                <p class="text-xs text-gray-600 dark:text-gray-400 mt-0.5 truncate">
                                    <span class="inline-flex items-center gap-1.5">
                                        <span class="w-1.5 h-1.5 rounded-full {{ $__sc[$key]['dot'] }} shrink-0"></span>
                                        {{ $act->pevaluacion?->pensum?->asignatura?->name ?? '—' }}
                                    </span>
                                    &middot;
                                    {{ $act->pevaluacion?->profesor?->lastname }} {{ $act->pevaluacion?->profesor?->name }}
                                </p>
                            </div>
                        </div>
                        <div class="flex items-center gap-2 shrink-0">
                            <span class="text-xs text-gray-400 dark:text-gray-500 whitespace-nowrap">
                                @if($log->event === 'COMPLETE')
                                    Completado
                                @else
                                    {{ $log->created_at->diffForHumans() }}
                                @endif
                            </span>
                            <svg class="w-4 h-4 text-gray-300 dark:text-gray-600 group-hover:text-emerald-500 transition-colors" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                            </svg>
                        </div>
                    </div>
                </a>
            @endforeach
        </div>
    </section>
    @elseif($suggestedActivities->isNotEmpty())
    <section>
        <div class="flex items-center gap-2 mb-2">
            <svg class="w-4 h-4 text-emerald-400" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
            </svg>
            <h2 class="text-base font-display font-bold text-gray-900 dark:text-white">Publicaciones Recientes</h2>
        </div>
        <p class="text-xs text-gray-600 dark:text-gray-400 mb-4">Actividades publicadas más recientes</p>
        <div class="space-y-2">
            @foreach($suggestedActivities as $activity)
            @php $key = $__scKey($activity->pevaluacion?->pensum?->asignatura?->name); @endphp
            <a href="{{ route('student.lms.activity', $activity) }}"
               class="group block bg-white dark:bg-gray-800/50 border border-gray-200 dark:border-gray-700 rounded-xl p-5 shadow-sm transition-all duration-200 ease-out hover:-translate-y-0.5 hover:shadow-md hover:border-emerald-500/40 motion-reduce:transform-none motion-reduce:transition-none focus-visible:ring-2 ring-emerald-500/50 focus-visible:ring-offset-2">
                <div class="flex items-start justify-between gap-3">
                    <div class="flex items-start gap-3 min-w-0">
                        <div class="w-9 h-9 rounded-lg flex items-center justify-center shrink-0 mt-0.5 {{ $__sc[$key]['chip'] }}">
                            <svg class="w-4 h-4" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/>
                            </svg>
                        </div>
                        <div class="min-w-0">
                            <p class="text-base font-display font-medium text-gray-900 dark:text-white group-hover:text-emerald-600 dark:group-hover:text-emerald-400 transition-colors truncate">
                                {{ $activity->topic ?? 'Actividad sin título' }}
                            </p>
                            <p class="text-xs text-gray-600 dark:text-gray-400 mt-0.5 truncate">
                                <span class="inline-flex items-center gap-1.5">
                                    <span class="w-1.5 h-1.5 rounded-full {{ $__sc[$key]['dot'] }} shrink-0"></span>
                                    {{ $activity->pevaluacion?->pensum?->asignatura?->name ?? '—' }}
                                </span>
                                &middot;
                                {{ $activity->pevaluacion?->profesor?->lastname }} {{ $activity->pevaluacion?->profesor?->name }}
                            </p>
                        </div>
                    </div>
                    <div class="flex items-center gap-2 shrink-0">
                        <span class="text-xs text-gray-400 dark:text-gray-500 whitespace-nowrap">
                            {{ $activity->lmsPublication?->publish_at?->diffForHumans() }}
                        </span>
                        <svg class="w-4 h-4 text-gray-300 dark:text-gray-600 group-hover:text-emerald-500 transition-colors" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </div>
                </div>
            </a>
            @endforeach
        </div>
    </section>
    @endif

    {{-- 3. Próximas publicaciones (publish_at = fecha más relevante para el estudiante) --}}
    @if($upcoming->isNotEmpty())
    <section>
        <div class="flex items-center gap-2 mb-4">
            <svg class="w-4 h-4 text-emerald-400" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <h2 class="text-base font-display font-bold text-gray-900 dark:text-white">Próximas Publicaciones</h2>
        </div>

        <div class="space-y-2">
            @foreach($upcoming as $activity)
                @php
                    $publishAt = $activity->lmsPublication?->publish_at;
                    $daysLeft = $publishAt
                        ? now()->startOfDay()->diffInDays($publishAt->copy()->startOfDay(), false)
                        : null;
                    $key = $__scKey($activity->pevaluacion?->pensum?->asignatura?->name);
                @endphp
                <a href="{{ route('student.lms.activity', $activity) }}"
                   class="group block bg-white dark:bg-gray-800/50 border border-gray-200 dark:border-gray-700 rounded-xl p-5 shadow-sm transition-all duration-200 ease-out hover:-translate-y-0.5 hover:shadow-md hover:border-emerald-500/40 motion-reduce:transform-none motion-reduce:transition-none focus-visible:ring-2 ring-emerald-500/50 focus-visible:ring-offset-2">
                    <div class="flex items-start justify-between gap-3">
                        <div class="flex items-start gap-3 min-w-0">
                            <div class="w-9 h-9 rounded-lg flex items-center justify-center shrink-0 mt-0.5 {{ $__sc[$key]['chip'] }}">
                                <svg class="w-4 h-4" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                            <div class="min-w-0">
                                <div class="flex items-center gap-2">
                                    <p class="text-base font-display font-medium text-gray-900 dark:text-white group-hover:text-emerald-600 dark:group-hover:text-emerald-400 transition-colors truncate">
                                        {{ $activity->topic ?? 'Actividad sin título' }}
                                    </p>
                                    <span class="shrink-0 inline-flex items-center gap-1 px-1.5 py-0.5 rounded-full text-xs font-semibold uppercase tracking-wider text-emerald-700 dark:text-emerald-300 bg-emerald-100 dark:bg-emerald-500/10 border border-emerald-300 dark:border-emerald-500/30">
                                        <svg class="w-2.5 h-2.5" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                        Vista previa
                                    </span>
                                </div>
                                <p class="text-xs text-gray-600 dark:text-gray-400 mt-0.5 truncate">
                                    <span class="inline-flex items-center gap-1.5">
                                        <span class="w-1.5 h-1.5 rounded-full {{ $__sc[$key]['dot'] }} shrink-0"></span>
                                        {{ $activity->pevaluacion?->pensum?->asignatura?->name ?? '—' }}
                                    </span>
                                    &middot;
                                    {{ $activity->pevaluacion?->lapso?->name ?? '' }}
                                </p>
                            </div>
                        </div>
                        <div class="shrink-0 text-xs font-medium whitespace-nowrap px-2.5 py-1 rounded-full border bg-emerald-100 dark:bg-emerald-500/10 border-emerald-300 dark:border-emerald-500/30 text-emerald-700 dark:text-emerald-300">
                            @if(!$publishAt)
                                Próximamente
                            @elseif($publishAt->isToday())
                                Se publica hoy a las {{ $publishAt->format('H:i') }}
                            @elseif($daysLeft === 1)
                                Se publica mañana
                            @elseif($daysLeft <= 7)
                                Se publica en {{ $daysLeft }} días
                            @else
                                Se publica el {{ $publishAt->translatedFormat('j M') }}
                            @endif
                        </div>
                    </div>
                </a>
            @endforeach
        </div>
    </section>
    @endif

    {{-- 4. Todas las lecciones (búsqueda + filtro + paginación) --}}
    @if($allLessons->total() > 0 || $this->search !== '' || $this->subjectFilter !== '')
    <section>
        <div class="flex items-center gap-2 mb-1">
            <svg class="w-4 h-4 text-emerald-400" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
            </svg>
            <h2 class="text-base font-display font-bold text-gray-900 dark:text-white">Todas las Lecciones</h2>
            <span class="text-xs text-gray-400 dark:text-gray-500">({{ $allLessons->total() }})</span>
        </div>
        <p class="text-xs text-gray-600 dark:text-gray-400 mb-3">
            Tu catálogo completo, de la más reciente a la más antigua
        </p>

        {{-- Búsqueda + filtro por asignatura --}}
        <div class="flex flex-col sm:flex-row gap-2 mb-3">
            <div class="relative flex-1">
                <svg class="w-4 h-4 text-gray-400 dark:text-gray-500 absolute left-3 top-1/2 -translate-y-1/2 pointer-events-none" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                <input type="search"
                       wire:model.live.debounce.300ms="search"
                       placeholder="Buscar lección…"
                       class="w-full pl-9 pr-8 py-2 min-h-[44px] rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800/50 text-sm text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-emerald-500/30 focus:border-emerald-500">
                @if($this->search !== '')
                <button type="button"
                        wire:click="$set('search', '')"
                        aria-label="Limpiar búsqueda"
                        class="absolute right-2 top-1/2 -translate-y-1/2 p-0.5 rounded-full text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors">
                    <svg class="w-4 h-4" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
                @endif
            </div>

            <select wire:model.live="subjectFilter"
                    class="sm:w-52 py-2 px-3 min-h-[44px] rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800/50 text-sm text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-emerald-500/30 focus:border-emerald-500">
                <option value="">Todas las asignaturas</option>
                @foreach($subjects as $subject)
                    <option value="{{ $subject }}">{{ $subject }}</option>
                @endforeach
            </select>
        </div>

        {{-- Leyenda de estado --}}
        <div class="flex items-center gap-3 mb-2 text-xs text-gray-400 dark:text-gray-500">
            <span class="inline-flex items-center gap-1.5">
                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Publicada
            </span>
            <span class="inline-flex items-center gap-1.5">
                <span class="w-1.5 h-1.5 rounded-full bg-amber-400"></span> Vista previa
            </span>
        </div>

        @if($allLessons->isNotEmpty())
        {{-- G2: skeleton de carga mientras la búsqueda / filtro / paginación
             actualizan la lista. Reusa el patrón de student-resource-card.blade.php
             (bg-gray-100 animate-pulse). Las filas son <div> (NO <li>): los tests C1
             cortan el HTML crudo por <li>/</li> para leer las estrellas de logros y un
             skeleton con <li> rompería el slice. Tampoco lleva texto real de lecciones.
             Scoped con wire:target para que el wire:poll.10s del home no lo haga
             parpadear cada 10s. --}}
        <div wire:loading.delay.shorter
             wire:target="search, subjectFilter, gotoPage"
             aria-hidden="true"
             class="divide-y divide-gray-100 dark:divide-gray-800">
            @for($i = 0; $i < 3; $i++)
            <div class="flex items-center justify-between gap-3 py-2 min-h-[44px]">
                <span class="flex items-center gap-2.5 min-w-0 flex-1">
                    <span class="w-1.5 h-1.5 rounded-full bg-gray-200 dark:bg-gray-700 shrink-0"></span>
                    <span class="h-4 w-40 sm:w-56 rounded bg-gray-100 dark:bg-gray-700/60 animate-pulse"></span>
                    <span class="hidden md:inline h-3 w-24 rounded bg-gray-100 dark:bg-gray-700/60 animate-pulse"></span>
                </span>
                <span class="shrink-0 flex items-center gap-3">
                    <span class="flex items-center gap-0.5">
                        <span class="w-3.5 h-3.5 rounded-full bg-gray-100 dark:bg-gray-700/60 animate-pulse"></span>
                        <span class="w-3.5 h-3.5 rounded-full bg-gray-100 dark:bg-gray-700/60 animate-pulse"></span>
                        <span class="w-3.5 h-3.5 rounded-full bg-gray-100 dark:bg-gray-700/60 animate-pulse"></span>
                    </span>
                    <span class="w-12 h-1 rounded-full bg-gray-200 dark:bg-gray-700"></span>
                </span>
            </div>
            @endfor
        </div>
        <ul class="divide-y divide-gray-100 dark:divide-gray-800"
            wire:loading.remove
            wire:target="search, subjectFilter, gotoPage">
            @foreach($allLessons as $activity)
                @php
                    $isPreview = $activity->lmsPublication?->isPreviewToStudents();
                    $row = $rowMeta[$activity->id] ?? null;
                    $earned = $row ? (int) $row['completed'] + (int) $row['commented'] + (int) $row['downloaded'] : 0;
                    $lessonKey = $__scKey($activity->pevaluacion?->pensum?->asignatura?->name);
                @endphp
            <li>
                <a href="{{ route('student.lms.activity', $activity) }}"
                   class="group flex items-center justify-between gap-3 py-2 min-h-[44px] rounded-lg focus-visible:ring-2 ring-emerald-500/50 focus-visible:ring-offset-2">
                    <span class="flex items-center gap-2.5 min-w-0">
                        <span @class([
                            'w-1.5 h-1.5 rounded-full shrink-0',
                            'bg-emerald-500' => !$isPreview,
                            'bg-amber-400' => $isPreview,
                        ])></span>
                        <span class="text-base font-display font-medium text-gray-700 dark:text-gray-300 group-hover:text-emerald-600 dark:group-hover:text-emerald-400 truncate transition-colors">
                            {{ $activity->topic ?? 'Actividad sin título' }}
                        </span>
                        @if($isPreview)
                            <span class="shrink-0 inline-flex items-center px-1.5 py-0.5 rounded-full text-xs font-semibold uppercase tracking-wider text-amber-700 dark:text-amber-300 bg-amber-100 dark:bg-amber-500/10 border border-amber-300 dark:border-amber-500/30">
                                Vista previa
                            </span>
                        @endif
                        <span class="hidden md:inline-flex items-center gap-1.5 text-xs text-gray-600 dark:text-gray-400 truncate">
                            <span class="w-1.5 h-1.5 rounded-full {{ $__sc[$lessonKey]['dot'] }} shrink-0"></span>
                            {{ $activity->pevaluacion?->pensum?->asignatura?->name ?? '' }}
                        </span>
                    </span>
                    {{-- Estrellas de logros (C1): completada / comentario aprobado / descarga --}}
                    <span class="shrink-0 inline-flex flex-col items-end gap-1" aria-hidden="true">
                        <span class="flex items-center gap-0.5">
                            @foreach(['completed', 'commented', 'downloaded'] as $key)
                            <svg @class([
                                'w-3.5 h-3.5',
                                'text-emerald-500' => $row && $row[$key],
                                'text-gray-300 dark:text-gray-600' => !$row || !$row[$key],
                            ]) fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.363-1.118l-2.8-2.034c-.784-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                            </svg>
                            @endforeach
                        </span>
                        <span class="w-12 h-1 bg-gray-200 dark:bg-gray-700 rounded-full overflow-hidden">
                            <span class="block h-full bg-emerald-500 rounded-full" style="width: {{ round($earned / 3 * 100) }}%"></span>
                        </span>
                    </span>
                    <span class="sr-only">{{ $earned }} de 3 logros</span>
                    <span class="shrink-0 text-xs text-gray-400 dark:text-gray-500 whitespace-nowrap">
                        {{ $activity->lmsPublication?->publish_at?->translatedFormat('j M Y') }}
                    </span>
                </a>
            </li>
            @endforeach
        </ul>

        <div class="mt-4">
            {{ $allLessons->links() }}
        </div>
        @else
        <div class="text-center py-10 px-4 rounded-xl border border-dashed border-gray-200 dark:border-gray-700">
            @if($showMascot)
                <div class="flex justify-center mb-3">
                    <x-lms.mascot :variant="'idle'" :size="'sm'" :emphasis="$mascotEmphasis" />
                </div>
            @endif
            <p class="text-sm text-gray-600 dark:text-gray-400">
                @if($this->search !== '' && $this->subjectFilter !== '')
                    No encontramos lecciones para “<span class="font-semibold text-gray-700 dark:text-gray-300">{{ $this->search }}</span>” en {{ $this->subjectFilter }}.
                @elseif($this->search !== '')
                    No encontramos lecciones para “<span class="font-semibold text-gray-700 dark:text-gray-300">{{ $this->search }}</span>”.
                @else
                    No encontramos lecciones en {{ $this->subjectFilter }}.
                @endif
            </p>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Prueba con otra búsqueda o limpia los filtros.</p>
            <div class="mt-4 flex flex-wrap items-center justify-center gap-2">
                @if($this->search !== '')
                <button type="button"
                        wire:click="$set('search', '')"
                        class="inline-flex items-center gap-1.5 px-4 py-2 min-h-[44px] rounded-lg text-xs font-semibold text-white bg-emerald-600 hover:bg-emerald-500 transition-colors focus-visible:ring-2 ring-emerald-500/50 focus-visible:ring-offset-2 dark:focus-visible:ring-offset-gray-900">
                    <svg class="w-3.5 h-3.5" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                    </svg>
                    Vuelve a intentarlo
                </button>
                @endif
                <button type="button"
                        wire:click="resetFilters"
                        class="inline-flex items-center gap-1.5 px-4 py-2 min-h-[44px] rounded-lg text-xs font-semibold text-emerald-600 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-500/30 hover:bg-emerald-50 dark:hover:bg-emerald-500/10 transition-colors focus-visible:ring-2 ring-emerald-500/50 focus-visible:ring-offset-2 dark:focus-visible:ring-offset-gray-900">
                    <svg class="w-3.5 h-3.5" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                    Ver todas
                </button>
            </div>
        </div>
        @endif
    </section>
    @endif

    {{-- 5. Subject Distribution --}}
    @if($subjectDistribution->isNotEmpty())
    <section>
        <div class="flex items-center gap-2 mb-4">
            <svg class="w-4 h-4 text-emerald-400" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
            </svg>
            <h2 class="text-base font-display font-bold text-gray-900 dark:text-white">Distribución por Asignatura</h2>
        </div>

        <div class="bg-white dark:bg-gray-800/50 border border-gray-200 dark:border-gray-700 rounded-xl p-4 space-y-5 shadow-sm transition-all duration-200 ease-out">
            @foreach($subjectDistribution as $subject)
                @php
                    $pct = $subject['total'] > 0 ? round(($subject['completed'] / $subject['total']) * 100) : 0;
                    $key = $__scKey($subject['name']);
                @endphp
                <div>
                    <div class="flex items-center justify-between mb-1.5">
                        <span class="inline-flex items-center gap-1.5 text-sm font-medium text-gray-900 dark:text-white">
                            <span class="w-1.5 h-1.5 rounded-full {{ $__sc[$key]['dot'] }} shrink-0"></span>
                            {{ $subject['name'] }}
                        </span>
                        <span class="text-xs text-gray-600 dark:text-gray-400">
                            {{ $subject['completed'] }} de {{ $subject['total'] }}
                            <span class="text-gray-400 dark:text-gray-500 ml-0.5">· {{ $pct }}%</span>
                        </span>
                    </div>
                    <div class="w-full h-2 bg-gray-100 dark:bg-gray-700 rounded-full overflow-hidden">
                        <div class="h-full rounded-full transition-all duration-500"
                             style="width: {{ $pct }}%; background: {{ $__sc[$key]['gradient'] }};">
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </section>
    @endif

    {{-- 6. Tu actividad reciente --}}
    @if($recentComments->isNotEmpty() || $recentDownloads->isNotEmpty())
    <section>
        <div class="flex items-center gap-2 mb-4">
            <svg class="w-4 h-4 text-emerald-400" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
            </svg>
            <h2 class="text-base font-display font-bold text-gray-900 dark:text-white">Tu actividad reciente</h2>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
            @if($recentComments->isNotEmpty())
            <div class="bg-white dark:bg-gray-800/50 border border-gray-200 dark:border-gray-700 rounded-xl p-4 shadow-sm transition-all duration-200 ease-out">
                <p class="text-xs font-semibold uppercase tracking-wider text-gray-400 dark:text-gray-500 mb-3">Comentarios recientes</p>
                <ul class="space-y-3">
                    @foreach($recentComments as $comment)
                    @php $cAct = $comment->activity; @endphp
                    @if(!$cAct) @continue @endif
                    @php $cKey = $__scKey($cAct->pevaluacion?->pensum?->asignatura?->name); @endphp
                    <li>
                        <a href="{{ route('student.lms.activity', $cAct) }}" class="block group">
                            <p class="text-sm text-gray-800 dark:text-gray-200 line-clamp-2 group-hover:text-emerald-600 dark:group-hover:text-emerald-400 transition-colors">
                                {{ $comment->body }}
                            </p>
                            <p class="text-xs text-gray-400 dark:text-gray-500 mt-0.5 truncate">
                                {{ $cAct->topic }} &middot; <span class="inline-flex items-center gap-1.5"><span class="w-1.5 h-1.5 rounded-full {{ $__sc[$cKey]['dot'] }} shrink-0"></span>{{ $cAct->pevaluacion?->pensum?->asignatura?->name ?? '—' }}</span>
                            </p>
                        </a>
                    </li>
                    @endforeach
                </ul>
            </div>
            @endif

            @if($recentDownloads->isNotEmpty())
            <div class="bg-white dark:bg-gray-800/50 border border-gray-200 dark:border-gray-700 rounded-xl p-4 shadow-sm transition-all duration-200 ease-out">
                <p class="text-xs font-semibold uppercase tracking-wider text-gray-400 dark:text-gray-500 mb-3">Descargas recientes</p>
                <ul class="space-y-3">
                    @foreach($recentDownloads as $log)
                    @php $dAct = $log->activity; @endphp
                    @if(!$dAct) @continue @endif
                    @php $dKey = $__scKey($dAct->pevaluacion?->pensum?->asignatura?->name); @endphp
                    <li>
                        <a href="{{ route('student.lms.activity', $dAct) }}" class="block group">
                            <p class="text-sm text-gray-800 dark:text-gray-200 truncate group-hover:text-emerald-600 dark:group-hover:text-emerald-400 transition-colors">
                                {{ $downloadResources[$log->context_id] ?? $dAct->topic }}
                            </p>
                            <p class="text-xs text-gray-400 dark:text-gray-500 mt-0.5 truncate">
                                <span class="inline-flex items-center gap-1.5">
                                    <span class="w-1.5 h-1.5 rounded-full {{ $__sc[$dKey]['dot'] }} shrink-0"></span>
                                    {{ $dAct->pevaluacion?->pensum?->asignatura?->name ?? '—' }}
                                </span>
                                &middot; {{ $log->created_at->diffForHumans() }}
                            </p>
                        </a>
                    </li>
                    @endforeach
                </ul>
            </div>
            @endif
        </div>
    </section>
    @endif

    {{-- Movimiento reducido --}}
    <style>
        @media (prefers-reduced-motion: reduce) {
            *, *::before, *::after {
                animation-duration: 0.01ms !important;
                animation-iteration-count: 1 !important;
                transition-duration: 0.01ms !important;
                scroll-behavior: auto !important;
            }
        }
    </style>
</div>
