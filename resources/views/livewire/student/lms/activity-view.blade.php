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

<div class="max-w-3xl mx-auto px-3 sm:px-6 md:px-8 py-4 sm:py-6 md:py-8 space-y-4 sm:space-y-6"
     x-data="readingNav()">

    {{-- ═══════════════════════ READING PROGRESS ═══════════════════════ --}}
    {{-- La barra de progreso va PEGADA al navbar global (top-14): primero la
         barra, después el toggle de modo. Ambos sticky; la barra queda
         justo debajo del nav header. --}}
    <div class="sticky top-14 z-20 -mx-3 sm:-mx-6 md:-mx-8 -mt-4 sm:-mt-6 md:-mt-8">
        <div x-show="Alpine.store('lmsView').mode === 'scroll'"
             role="progressbar"
             aria-label="Progreso de lectura"
             :aria-valuenow="progress"
             aria-valuemin="0"
             aria-valuemax="100"
             class="h-[3px] bg-gradient-to-r from-emerald-600 to-emerald-400 transition-[width] duration-150 ease-out"
             :style="`width: ${progress}%`"></div>
    </div>

    {{-- Toggle de modo de lectura — flotante abajo a la derecha --}}
    <div class="fixed bottom-6 right-6 z-50 print:hidden">
        <div class="inline-flex items-center gap-1 rounded-full border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800/50 p-1 shadow-lg"
             role="radiogroup" aria-label="Modo de lectura">
            <button type="button"
                    role="radio"
                    :aria-checked="Alpine.store('lmsView').mode === 'scroll'"
                    :class="Alpine.store('lmsView').mode === 'scroll' ? 'bg-emerald-600 text-white' : 'text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700'"
                    class="rounded-full px-3 py-1 text-xs font-semibold transition-colors focus-visible:ring-2 ring-emerald-500/50 focus-visible:ring-offset-2"
                    @click="Alpine.store('lmsView').set('scroll')">
                Deslizar
            </button>
            <button type="button"
                    role="radio"
                    :aria-checked="Alpine.store('lmsView').mode === 'pdf'"
                    :class="Alpine.store('lmsView').mode === 'pdf' ? 'bg-emerald-600 text-white' : 'text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700'"
                    class="rounded-full px-3 py-1 text-xs font-semibold transition-colors disabled:cursor-not-allowed disabled:opacity-40 focus-visible:ring-2 ring-emerald-500/50 focus-visible:ring-offset-2"
                    @click="Alpine.store('lmsView').set('pdf')">
                PDF
            </button>
        </div>
    </div>

    {{-- ═══════════════════════ BREADCRUMB (D1 · Pan rallado) ═══════════════════════ --}}
    @php
        $materia = $activity->pevaluacion?->pensum?->asignatura?->name;
        $materiaKey = $__scKey($materia);
    @endphp
    <nav aria-label="Ruta de navegación" class="px-1">
        <ol class="flex items-center flex-wrap gap-x-1 gap-y-0.5 text-[11px] sm:text-xs">
            <li>
                <a href="{{ route('student.lms.home') }}"
                   class="rounded font-semibold text-gray-500 dark:text-gray-400 hover:text-emerald-700 dark:hover:text-emerald-300 transition-colors focus-visible:ring-2 ring-emerald-500/50 focus-visible:ring-offset-2">
                    Inicio
                </a>
            </li>
            <li aria-hidden="true" class="flex items-center">
                <svg class="w-3 h-3 text-gray-300 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
            </li>
            <li>
                <a href="{{ route('student.lms.lessons') }}"
                   class="rounded font-semibold text-gray-500 dark:text-gray-400 hover:text-emerald-700 dark:hover:text-emerald-300 transition-colors focus-visible:ring-2 ring-emerald-500/50 focus-visible:ring-offset-2">
                    Lecciones
                </a>
            </li>
            @if($materia)
            <li aria-hidden="true" class="flex items-center">
                <svg class="w-3 h-3 text-gray-300 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
            </li>
            <li>
                <span class="inline-flex items-center gap-1 {{ $__sc[$materiaKey]['text'] }}"><span class="w-1.5 h-1.5 rounded-full {{ $__sc[$materiaKey]['dot'] }} shrink-0"></span>{{ $materia }}</span>
            </li>
            @endif
            <li aria-hidden="true" class="flex items-center">
                <svg class="w-3 h-3 text-gray-300 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
            </li>
            <li>
                <span aria-current="page" class="text-gray-900 dark:text-white font-bold">{{ $activity->topic ?? 'Actividad' }}</span>
            </li>
        </ol>
    </nav>

    {{-- ═══════════════════════════════════════ BACK NAV ═══════════════════════════════════════ --}}
    <nav class="flex items-center gap-3 px-1">
        <a href="{{ route('student.lms.lessons') }}"
           class="inline-flex items-center gap-1.5 px-3 py-1.5 min-h-[44px] rounded-lg text-xs font-semibold text-gray-700 dark:text-gray-200 hover:text-emerald-700 dark:hover:text-emerald-300 hover:bg-emerald-50 dark:hover:bg-emerald-500/10 border border-transparent hover:border-emerald-200 dark:hover:border-emerald-500/20 transition-all duration-200 focus-visible:ring-2 ring-emerald-500/50 focus-visible:ring-offset-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Volver a Lecciones
        </a>
    </nav>

    {{-- ═══════════════════════════════════════ HEADER ═══════════════════════════════════════ --}}
    <header class="bg-white dark:bg-gray-800/50 rounded-xl border border-gray-200 dark:border-gray-700/60 p-4 sm:p-6">
        <div class="flex items-start justify-between gap-3">
            <div class="min-w-0 flex-1">

                {{-- Title --}}
                <h1 class="text-lg sm:text-xl md:text-2xl font-display font-bold text-gray-900 dark:text-white leading-tight">
                    {{ $activity->topic ?? 'Actividad' }}
                </h1>

                {{-- Meta --}}
                <div class="flex flex-wrap items-center gap-x-3 gap-y-1 mt-2 text-xs sm:text-sm text-gray-500 dark:text-gray-400">
                    @php $pub = $activity->lmsPublication; @endphp
                    @if($pub && $pub->publish_at)
                        <span class="inline-flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            {{ \Carbon\Carbon::parse($pub->publish_at)->translatedFormat('j M Y') }}
                            @if($pub->unpublish_at)
                                – {{ \Carbon\Carbon::parse($pub->unpublish_at)->translatedFormat('j M Y') }}
                            @endif
                        </span>
                    @endif
                    @if($isPreview)
                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] sm:text-[11px] font-semibold bg-amber-100 dark:bg-amber-500/10 text-amber-800 dark:text-amber-300 border border-amber-300 dark:border-amber-500/30">
                            <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                            Vista previa
                        </span>
                    @elseif($activity->lmsPublication?->status === 'PUBLISHED')
                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] sm:text-[11px] font-semibold bg-emerald-100 dark:bg-emerald-500/10 text-emerald-800 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-500/30">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                            Publicada
                        </span>
                    @endif
                </div>
            </div>

            {{-- Status badge + estrellas de logros (C1) --}}
            <div class="shrink-0 flex flex-col items-end gap-2">
                @if($completed)
                    <span class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg text-[11px] sm:text-xs font-bold bg-emerald-100 dark:bg-emerald-500/10 text-emerald-800 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-500/30">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                        </svg>
                        Completada
                    </span>
                @endif
                @php
                    $starFlags = ['completed' => $completed, 'commented' => $isCommented, 'downloaded' => $hasDownload];
                    $earned = (int) $completed + (int) $isCommented + (int) $hasDownload;
                @endphp
                <span class="inline-flex items-center gap-0.5" aria-hidden="true">
                    @foreach($starFlags as $flag)
                    <svg @class([
                        'w-4 h-4',
                        'text-emerald-500' => $flag,
                        'text-gray-300 dark:text-gray-600' => !$flag,
                    ]) fill="currentColor" viewBox="0 0 20 20">
                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.363-1.118l-2.8-2.034c-.784-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                    </svg>
                    @endforeach
                </span>
                <span class="sr-only">{{ $earned }} de 3 logros</span>
            </div>
        </div>

        {{-- Description --}}
        @if($activity->description)
            <p class="mt-3 sm:mt-4 text-sm sm:text-[15px] text-gray-700 dark:text-gray-200 leading-relaxed border-t border-gray-200 dark:border-gray-700/40 pt-3 sm:pt-4">
                {{ $activity->description }}
            </p>
        @endif
    </header>

    {{-- F3 · Micro-copia infantil: CTA de inicio "Pulsa para empezar" en modo
         lectura (5–8). Desplaza suavemente a la 1ª sección vía goTo() del
         componente Alpine readingNav. Solo antes de completar la lección y
         cuando hay secciones; para 9–12/13–15 no aparece (el TOC ya guía). --}}
    @if($modoLectura && !$completed && $sections->isNotEmpty())
        <div class="px-1">
            <button type="button"
                    @click.prevent="goTo({{ $sections->first()->id }})"
                    class="w-full inline-flex items-center justify-center gap-3 px-6 py-4 min-h-[56px] text-lg font-bold text-white bg-gradient-to-br from-emerald-600 to-emerald-500 hover:from-emerald-500 hover:to-emerald-400 shadow-md shadow-emerald-600/25 rounded-2xl transition-all duration-200 active:scale-[0.98] focus-visible:ring-2 ring-emerald-500/50 focus-visible:ring-offset-2 group">
                <span class="sr-only">{{ $sections->first()->title }}</span>
                <svg class="w-5 h-5 shrink-0 transition-transform duration-200 group-hover:translate-y-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <span>Pulsa para empezar</span>
            </button>
        </div>
    @endif

    {{-- ═══════════════════════════════════════ PREVIEW BANNER ═══════════════════════════════════════ --}}
    @if($isPreview)
        <div class="bg-amber-50 dark:bg-amber-500/10 border border-amber-200 dark:border-amber-500/30 rounded-xl p-3 sm:p-4 flex items-start gap-3"
             role="status">
            <svg class="w-5 h-5 text-amber-600 dark:text-amber-400 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
            </svg>
            <div class="min-w-0">
                <p class="text-sm font-bold text-amber-900 dark:text-amber-300">Vista previa de la lección</p>
                <p class="text-xs sm:text-[13px] text-amber-700 dark:text-amber-400 leading-relaxed mt-0.5">
                    @if($activity->lmsPublication?->publish_at)
                        Esta lección se publicará <strong>{{ $activity->lmsPublication->humanPublishIn() }}</strong>, el <strong>{{ \Carbon\Carbon::parse($activity->lmsPublication->publish_at)->translatedFormat('j M Y \a \l\a\s H:i') }}</strong>, y por ahora solo puedes ver la primera sección.
                    @else
                        Esta lección aún no está publicada por completo. Solo puedes ver la primera sección.
                    @endif
                </p>
            </div>
        </div>
    @endif

    <div x-show="Alpine.store('lmsView').mode === 'scroll'">

    {{-- ═══════════════════════ TABLA DE CONTENIDO ═══════════════════════ --}}
    @if($sections->count() > 1)
        <nav class="bg-white dark:bg-gray-800/50 rounded-xl border border-gray-200 dark:border-gray-700/50 overflow-hidden">
            <div class="flex items-center gap-2 px-4 py-2.5 sm:py-3 border-b border-gray-200 dark:border-gray-700/40">
                <svg class="w-4 h-4 {{ $__sc[$materiaKey]['text'] }} shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h10"/>
                </svg>
                <span class="w-1.5 h-1.5 rounded-full {{ $__sc[$materiaKey]['dot'] }} shrink-0"></span>
                <span class="text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-200">Contenido</span>
                <span class="ml-auto text-[11px] font-medium text-gray-400 dark:text-gray-500 tabular-nums">{{ $sections->count() }} secciones</span>
            </div>
            <ol class="grid grid-cols-1 sm:grid-cols-2 gap-1 p-2 sm:p-3">
                @foreach($sections as $section)
                    <li>
                        <a href="#seccion-{{ $section->id }}"
                           @click.prevent="goTo({{ $section->id }})"
                           :aria-current="activeId === {{ $section->id }} ? 'location' : undefined"
                           :class="activeId === {{ $section->id }}
                               ? 'bg-emerald-50 dark:bg-emerald-500/10 text-emerald-800 dark:text-emerald-300 border-emerald-200 dark:border-emerald-500/30'
                               : 'text-gray-600 dark:text-gray-300 border-transparent hover:bg-gray-50 dark:hover:bg-gray-700/40'"
                           class="flex items-center gap-2 px-2.5 py-2 min-h-[44px] rounded-lg text-[13px] font-medium border transition-colors duration-150 focus-visible:ring-2 ring-emerald-500/50 focus-visible:ring-offset-2">
                            <span class="shrink-0 inline-flex items-center justify-center w-5 h-5 rounded-full bg-gray-100 dark:bg-gray-700/60 text-gray-500 dark:text-gray-300 text-[10px] font-bold"
                                  :class="activeId === {{ $section->id }} ? 'bg-emerald-100 dark:bg-emerald-500/20 text-emerald-700 dark:text-emerald-300' : ''">
                                {{ $loop->iteration }}
                            </span>
                            <span class="truncate">{{ $section->title }}</span>
                        </a>
                    </li>
                @endforeach
            </ol>
        </nav>
    @endif

    {{-- ═══════════════════════════════════════ SECTIONS ═══════════════════════════════════════ --}}
    @forelse($sections as $section)
        @php
            $sectionUpper = mb_strtoupper($section->title ?? '');
            $bgHue = '';
            $accentColor = 'mint';
            $accentDot = 'bg-emerald-500';
            $accentRing = 'ring-emerald-500/20';
            $badgeLabel = null;
            $badgeClass = '';

            if (preg_match('/\b(INICIO|INTRODUCCI[OÓ]N|APERTURA|BIENVENIDA|PRESENTACI[OÓ]N)\b/', $sectionUpper)) {
                $badgeLabel = 'INICIO';
                $badgeClass = 'bg-blue-50 dark:bg-blue-500/10 text-blue-700 dark:text-blue-400 border border-blue-200 dark:border-blue-500/20';
                $accentColor = 'blue';
                $accentDot = 'bg-blue-500';
                $accentRing = 'ring-blue-500/20';
            } elseif (preg_match('/\b(DESARROLLO|ACTIVIDAD|CONTENIDO|EXPLICACI[OÓ]N|EJERCICIO|PR[AÁ]CTICA|AN[AÁ]LISIS|PROFUNDIZACI[OÓ]N|REFLEXI[OÓ]N|LECTURA|TEMA)\b/', $sectionUpper)) {
                $badgeLabel = 'DESARROLLO';
                $badgeClass = 'bg-emerald-100 dark:bg-emerald-500/10 text-emerald-800 dark:text-emerald-300 border border-emerald-300 dark:border-emerald-500/30';
                $accentColor = 'mint';
                $accentDot = 'bg-emerald-500';
                $accentRing = 'ring-emerald-500/20';
            } elseif (preg_match('/\b(CIERRE|CONCLUSI[OÓ]N|RESUMEN|EVALUACI[OÓ]N|REPASO|S[IÍ]NTESIS|FINAL|RETROALIMENTACI[OÓ]N)\b/', $sectionUpper)) {
                $badgeLabel = 'CIERRE';
                $badgeClass = 'bg-amber-50 dark:bg-amber-500/10 text-amber-700 dark:text-amber-400 border border-amber-200 dark:border-amber-500/20';
                $accentColor = 'amber';
                $accentDot = 'bg-amber-500';
                $accentRing = 'ring-amber-500/20';
            }

            $contentCount = $section->visibleContents->count();
        @endphp

        <section id="seccion-{{ $section->id }}" wire:key="section-{{ $section->id }}"
                 class="my-1 sm:my-1.5 bg-white dark:bg-gray-800/50 rounded-xl border border-gray-200 dark:border-gray-700/60 overflow-hidden p-1 sm:p-1.5">

            {{-- Section header --}}
            <div class="flex items-center gap-2 px-3 sm:px-5 py-2.5 sm:py-3.5 border-b border-emerald-200 dark:border-gray-700/40 bg-emerald-50 dark:bg-gray-800/70 rounded-lg rounded-b-none">
                <span class="w-1 h-6 rounded-full {{ $accentDot }} shrink-0"></span>
                <h2 class="text-sm sm:text-[15px] font-display font-bold text-gray-900 dark:text-white flex-1 min-w-0">
                    {{ $section->title }}
                </h2>
                @if($badgeLabel)
                    <span class="shrink-0 inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider {{ $badgeClass }}">
                        {{ $badgeLabel }}
                    </span>
                @endif
                @if($contentCount)
                    <span class="shrink-0 text-[11px] font-semibold text-emerald-800 dark:text-gray-200 bg-emerald-100 dark:bg-gray-700/70 px-2.5 py-0.5 rounded-full border border-emerald-300 dark:border-gray-600">
                        {{ $contentCount }} {{ $contentCount === 1 ? 'paso' : 'pasos' }}
                    </span>
                @endif
            </div>

            {{-- Section body --}}
            <div class="p-3 sm:p-5 space-y-3 sm:space-y-4">
                @foreach($section->visibleContents as $idx => $content)
                    @include('livewire.student.lms._content-renderer', [
                        'content' => $content,
                        'stepNum' => $idx + 1,
                        'isLast' => $loop->last,
                    ])
                @endforeach

                {{-- HTML Embeds vinculados a esta sección --}}
                @php
                    $sectionEmbeds = $htmlEmbeds->filter(fn($e) => $e->section_id === $section->id);
                @endphp
                @if($sectionEmbeds->isNotEmpty())
                    <div class="space-y-3 pt-2 border-t border-gray-200">
                        @foreach($sectionEmbeds as $embed)
                            <div class="bg-white border border-fuchsia-200 rounded-xl p-2.5 sm:p-3 html-embed-item">
                                @if($embed->title)
                                    <h4 class="text-sm font-bold text-gray-900 mb-2">{{ $embed->title }}</h4>
                                @endif
                                @if($embed->is_mermaid ?? false)
                                    <div wire:ignore x-data="mermaidEmbed()"
                                         data-mermaid-code="{{ app(\App\Services\Lms\LmsContentClassifier::class)->extractMermaidCode($embed->html_content) }}"
                                         class="w-full bg-white rounded-lg p-4 overflow-x-auto border border-slate-200/60 flex flex-col mermaid-fill-height relative">
                                        <div x-ref="target" class="w-full min-h-0"></div>
                                    </div>
                                @else
                                    <div class="text-[17px] text-gray-900 leading-7 prose prose-sm max-w-none html-embed-content">
                                        {!! $embed->html_content !!}
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @endif

                {{-- Recursos vinculados a esta sección --}}
                @php
                    $secResources = $resources->filter(fn($r) => $r->section_id === $section->id);
                @endphp
                @if($secResources->isNotEmpty())
                    <div class="space-y-2 pt-2 border-t border-gray-200">
                        @foreach($secResources as $resource)
                            <x-lms.student-resource-card :resource="$resource" :activity="$activity" />
                        @endforeach
                    </div>
                @endif

                {{-- Enlaces vinculados a esta sección --}}
                @php
                    $secLinks = $links->filter(fn($l) => $l->section_id === $section->id);
                @endphp
                @if($secLinks->isNotEmpty())
                    <div class="space-y-2 pt-2 border-t border-gray-200">
                        @foreach($secLinks as $link)
                            <a href="{{ $link->url }}" target="_blank" rel="noopener noreferrer"
                               class="flex items-center gap-3 p-3 min-h-[44px] bg-white border border-blue-200 rounded-xl hover:bg-blue-50 transition-colors group focus-visible:ring-2 ring-emerald-500/50 focus-visible:ring-offset-2">
                                <div class="w-8 h-8 rounded-lg bg-blue-100 flex items-center justify-center shrink-0">
                                    <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                                    </svg>
                                </div>
                                <div class="min-w-0 flex-1">
                                    <p class="text-sm font-medium text-blue-800 truncate">{{ $link->title }}</p>
                                    <p class="text-xs text-blue-500 truncate">{{ $link->url }}</p>
                                </div>
                                <span class="shrink-0 text-[10px] font-medium text-blue-500 bg-blue-100 px-2 py-0.5 rounded border border-blue-200">{{ $link->link_type ?? 'Enlace' }}</span>
                            </a>
                        @endforeach
                    </div>
                @endif
            </div>
        </section>

        {{-- F3 · Micro-copia infantil: empujón "¡Ya casi terminas!" tras la
             última sección en modo lectura (5–8), solo si aún no se completa.
             Check SVG, no emoji (se corrompen en esta base). --}}
        @if($modoLectura && !$completed && $loop->last)
            <div class="bg-emerald-50 dark:bg-emerald-500/10 border border-emerald-200 dark:border-emerald-500/30 rounded-xl p-4 sm:p-5 flex items-start gap-3">
                <div class="w-9 h-9 rounded-full bg-emerald-100 dark:bg-emerald-500/20 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                    </svg>
                </div>
                <div class="min-w-0">
                    <p class="text-base font-bold text-emerald-900 dark:text-emerald-300">¡Ya casi terminas!</p>
                    <p class="text-sm text-emerald-700 dark:text-emerald-400 leading-relaxed mt-0.5">Pulsa el botón de abajo cuando hayas terminado la lección.</p>
                </div>
            </div>
        @endif
    @empty
        <section class="bg-white dark:bg-gray-800/50 rounded-xl border border-dashed border-gray-200 dark:border-gray-700/50 p-6 sm:p-10 text-center"
                 aria-live="polite">
            @if($showMascot)
                <div class="flex justify-center mb-3">
                    <x-lms.mascot :variant="'idle'" :size="'sm'" :emphasis="$mascotEmphasis" />
                </div>
            @endif
            <p class="text-sm font-medium text-gray-700 dark:text-gray-200">No hay contenido disponible</p>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Esta actividad no tiene secciones visibles.</p>
            <div class="mt-4">
                <a href="{{ route('student.lms.lessons') }}"
                   class="inline-flex items-center gap-1.5 px-4 py-2 min-h-[44px] rounded-lg text-xs font-semibold text-emerald-700 dark:text-emerald-300 bg-emerald-50 dark:bg-emerald-500/10 hover:bg-emerald-100 dark:hover:bg-emerald-500/20 border border-emerald-200 dark:border-emerald-500/20 transition-colors focus-visible:ring-2 ring-emerald-500/50 focus-visible:ring-offset-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Volver a las lecciones
                </a>
            </div>
        </section>
    @endforelse

    {{-- ═══════════════════════════════════════ RESOURCES (no vinculados) ═══════════════════════════════════════ --}}
    @php
        $unlinkedResources = $resources->filter(fn($r) => empty($r->section_id));
    @endphp
    @if($unlinkedResources->isNotEmpty())
        <section class="bg-white dark:bg-gray-800/50 rounded-xl border border-gray-200 dark:border-gray-700/50 overflow-hidden">
            <div class="flex items-center gap-2 px-3 sm:px-5 py-2.5 sm:py-3.5 border-b border-emerald-200 dark:border-gray-700/40 bg-emerald-50 dark:bg-gray-800/70">
                <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                </svg>
                <h2 class="text-sm font-bold text-gray-700 dark:text-gray-200 uppercase tracking-wider">Recursos descargables</h2>
                <span class="ml-auto text-[11px] text-gray-400 dark:text-gray-500">{{ $unlinkedResources->count() }} {{ $unlinkedResources->count() === 1 ? 'archivo' : 'archivos' }}</span>
            </div>
            <div class="p-3 sm:p-5 space-y-2">
                @foreach($unlinkedResources as $resource)
                    <x-lms.student-resource-card :resource="$resource" :activity="$activity" />
                @endforeach
            </div>
        </section>
    @endif

    {{-- ═══════════════════════════════════════ LINKS (no vinculados) ═══════════════════════════════════════ --}}
    @php
        $unlinkedLinks = $links->filter(fn($l) => empty($l->section_id));
    @endphp
    @if($unlinkedLinks->isNotEmpty())
        <section class="bg-white dark:bg-gray-800/50 rounded-xl border border-gray-200 dark:border-gray-700/50 overflow-hidden">
            <div class="flex items-center gap-2 px-3 sm:px-5 py-2.5 sm:py-3.5 border-b border-emerald-200 dark:border-gray-700/40 bg-emerald-50 dark:bg-gray-800/70">
                <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                </svg>
                <h2 class="text-sm font-bold text-gray-700 dark:text-gray-200 uppercase tracking-wider">Referencias y enlaces</h2>
            </div>
            <div class="p-3 sm:p-5 space-y-2">
                @foreach($unlinkedLinks as $link)
                    <a href="{{ $link->url }}" target="_blank" rel="noopener noreferrer"
                       class="flex items-center gap-3 p-3 bg-white border border-blue-200 rounded-xl hover:bg-blue-50 transition-colors group">
                        <div class="w-8 h-8 rounded-lg bg-blue-100 flex items-center justify-center shrink-0">
                            <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                            </svg>
                        </div>
                        <div class="min-w-0 flex-1">
                            <p class="text-sm font-medium text-blue-800 truncate">{{ $link->title }}</p>
                            @if($link->description)
                                <p class="text-xs text-gray-500 truncate">{{ $link->description }}</p>
                            @endif
                        </div>
                        <span class="shrink-0 text-[10px] font-medium text-blue-500 bg-blue-100 px-2 py-0.5 rounded border border-blue-200">{{ $link->link_type ?? 'Enlace' }}</span>
                    </a>
                @endforeach
            </div>
        </section>
    @endif

    {{-- ═══════════════════════════════════════ HTML EMBEDS (no vinculados) ═══════════════════════════════════════ --}}
    @php
        $unlinkedEmbeds = $htmlEmbeds->filter(fn($e) => empty($e->section_id));
    @endphp
    @if($unlinkedEmbeds->isNotEmpty())
        <section class="bg-white dark:bg-gray-800/50 rounded-xl border border-gray-200 dark:border-gray-700/50 overflow-hidden">
            <div class="flex items-center gap-2 px-3 sm:px-5 py-2.5 sm:py-3.5 border-b border-emerald-200 dark:border-gray-700/40 bg-emerald-50 dark:bg-gray-800/70">
                <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"/>
                </svg>
                <h2 class="text-sm font-bold text-gray-700 dark:text-gray-200 uppercase tracking-wider">Contenido embebido</h2>
            </div>
            <div class="p-2.5 sm:p-4 space-y-2">
                @foreach($unlinkedEmbeds as $embed)
                    <div class="bg-white border border-fuchsia-200 rounded-xl p-2.5 sm:p-3 html-embed-item">
                        @if($embed->title)
                            <h4 class="text-sm font-bold text-gray-900 mb-2">{{ $embed->title }}</h4>
                        @endif
                        @if($embed->is_mermaid ?? false)
                            <div wire:ignore x-data="mermaidEmbed()"
                                 data-mermaid-code="{{ app(\App\Services\Lms\LmsContentClassifier::class)->extractMermaidCode($embed->html_content) }}"
                                 class="w-full bg-white rounded-lg p-4 overflow-x-auto border border-slate-200/60 flex flex-col mermaid-fill-height relative">
                                <div x-ref="target" class="w-full min-h-0"></div>
                            </div>
                        @else
                            <div class="text-[17px] text-gray-900 leading-7 prose prose-sm max-w-none html-embed-content">
                                {!! $embed->html_content !!}
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        </section>
    @endif

    {{-- ═══════════════════════════════════════ COMMENTS ═══════════════════════════════════════ --}}
    @if($activity->lmsPublication?->allow_comments)
        <section class="bg-white dark:bg-gray-800/50 rounded-xl border border-gray-200 dark:border-gray-700/50 overflow-hidden">
            <div class="flex items-center gap-2 px-3 sm:px-5 py-2.5 sm:py-3.5 border-b border-emerald-200 dark:border-gray-700/40 bg-emerald-50 dark:bg-gray-800/70">
                <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                </svg>
                <h2 class="text-sm font-bold text-gray-700 dark:text-gray-200 uppercase tracking-wider">Comentarios</h2>
            </div>
            <div class="p-3 sm:p-5 space-y-3">
                {{-- Form --}}
                <form wire:submit="saveComment" class="flex items-end gap-2">
                    <textarea wire:model="newComment" placeholder="Escribe un comentario…" rows="2"
                              class="flex-1 resize-none bg-white dark:bg-gray-800 border border-gray-400 dark:border-gray-600 rounded-xl px-4 py-2.5 text-sm text-gray-900 dark:text-white placeholder-gray-600 dark:placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-emerald-500/30 focus:border-emerald-500/50 transition-all"
                              maxlength="1000"></textarea>
                    <button type="submit"
                            class="shrink-0 px-4 py-2.5 min-h-[44px] bg-emerald-600 hover:bg-emerald-500 text-white text-sm font-medium rounded-xl transition-colors focus-visible:ring-2 ring-emerald-500/50 focus-visible:ring-offset-2"
                            wire:loading.class="opacity-50 cursor-not-allowed">
                        <span wire:loading.remove wire:target="saveComment">Enviar</span>
                        <span wire:loading wire:target="saveComment">Enviando…</span>
                    </button>
                </form>
                @error('newComment') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror

                {{-- List --}}
                <div class="space-y-3">
                    @forelse($comments as $comment)
                        <div class="flex gap-3 p-3 rounded-xl bg-white dark:bg-gray-800/50">
                            <div class="w-8 h-8 rounded-full bg-emerald-100 dark:bg-emerald-500/10 flex items-center justify-center shrink-0">
                                <span class="text-xs font-bold text-emerald-800 dark:text-emerald-300">
                                    {{ strtoupper(mb_substr($comment->user?->profile?->firstname ?? $comment->user?->name ?? '?', 0, 1)) }}
                                </span>
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-2 flex-wrap">
                                    <span class="text-xs font-semibold text-gray-900 dark:text-white">
                                        {{ $comment->user?->profile?->firstname ?? $comment->user?->name ?? '—' }}
                                    </span>
                                    <span class="text-[10px] text-gray-400 dark:text-gray-500">
                                        {{ $comment->created_at->diffForHumans() }}
                                    </span>
                                </div>
                                <p class="text-sm text-gray-900 dark:text-gray-100 mt-1 leading-relaxed">{{ $comment->body }}</p>
                            </div>
                        </div>

                        {{-- Réplicas del profesor (anidadas) --}}
                        @if($comment->replies->isNotEmpty())
                            <div class="ml-10 sm:ml-12 pl-3 sm:pl-4 border-l-2 border-emerald-200 dark:border-emerald-500/30 space-y-2">
                                @foreach($comment->replies as $reply)
                                    <div class="flex gap-3 p-3 rounded-xl bg-emerald-50/60 dark:bg-emerald-500/5">
                                        <div class="w-7 h-7 rounded-full bg-emerald-500/20 flex items-center justify-center shrink-0">
                                            <span class="text-[10px] font-bold text-emerald-700 dark:text-emerald-300">
                                                {{ strtoupper(mb_substr($reply->user?->profile?->firstname ?? $reply->user?->name ?? '?', 0, 1)) }}
                                            </span>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <div class="flex items-center gap-2 flex-wrap">
                                                <span class="text-xs font-semibold text-emerald-800 dark:text-emerald-300">
                                                    {{ $reply->user?->profile?->firstname ?? $reply->user?->name ?? '—' }}
                                                </span>
                                                <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[9px] font-bold uppercase tracking-wider bg-emerald-500/15 text-emerald-600 dark:text-emerald-400">
                                                    Profesor
                                                </span>
                                                <span class="text-[10px] text-gray-400 dark:text-gray-500">
                                                    {{ $reply->created_at->diffForHumans() }}
                                                </span>
                                            </div>
                                            <p class="text-sm text-gray-800 dark:text-gray-100 mt-1 leading-relaxed">{{ $reply->body }}</p>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    @empty
                        <p class="text-sm text-gray-500 dark:text-gray-400 text-center py-6">No hay comentarios aún. ¡Sé el primero en comentar!</p>
                    @endforelse
                </div>
            </div>
        </section>
    @endif

    {{-- ═══════════════════════════════════════ BACK ═══════════════════════════════════════ --}}
    <div class="relative bg-white dark:bg-gray-800/50 border border-gray-200 dark:border-gray-700/50 rounded-xl p-4 sm:p-5">
        {{-- Accent bar --}}
        <div class="absolute top-0 left-4 right-4 h-px bg-gradient-to-r from-transparent via-emerald-400/40 to-transparent"></div>

        <div class="flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-3">
            <a href="{{ route('student.lms.lessons') }}"
               class="inline-flex items-center gap-2 px-4 py-2.5 text-sm font-semibold text-gray-700 dark:text-gray-200 hover:text-emerald-700 dark:hover:text-emerald-300 bg-gray-100 dark:bg-gray-800/50 border border-gray-300 dark:border-gray-600 rounded-xl hover:bg-emerald-50 dark:hover:bg-emerald-500/5 hover:border-emerald-300/70 dark:hover:border-emerald-500/20 transition-all duration-200 min-h-[44px] group focus-visible:ring-2 ring-emerald-500/50 focus-visible:ring-offset-2">
                <svg class="w-4 h-4 transition-transform duration-200 group-hover:-translate-x-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Volver a lecciones
            </a>

            @if(!$isPreview && $activity->lmsPublication?->status === 'PUBLISHED' && !$completed)
                <button wire:click="markComplete"
                        wire:loading.attr="disabled"
                        wire:target="markComplete"
                        class="inline-flex items-center justify-center gap-2 px-5 py-2.5 min-h-[48px] w-full sm:w-auto text-base font-bold text-white bg-gradient-to-br from-emerald-600 to-emerald-700 hover:from-emerald-500 hover:to-emerald-600 shadow-sm shadow-emerald-600/20 hover:shadow-emerald-500/30 rounded-xl transition-all duration-200 active:scale-[0.97] disabled:opacity-60 disabled:cursor-not-allowed disabled:active:scale-100 focus-visible:ring-2 ring-emerald-500/50 focus-visible:ring-offset-2">
                    <span wire:loading.remove wire:target="markComplete" class="flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                        </svg>
                        @if($modoLectura)
                            ¡Lo terminé!
                        @else
                            Marcar como completada
                        @endif
                    </span>
                    <span wire:loading wire:target="markComplete" class="flex items-center gap-2">
                        <svg class="w-4 h-4 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                        </svg>
                        Guardando…
                    </span>
                </button>
            @elseif($completed)
                <div class="inline-flex items-center gap-2 px-5 py-2.5 text-sm font-bold text-emerald-700 dark:text-emerald-300 bg-emerald-50 dark:bg-emerald-500/10 border border-emerald-200 dark:border-emerald-500/30 rounded-xl min-h-[44px] select-none">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Completada
                </div>
            @endif
        </div>
    </div>

    </div>

    <!-- PDF Mode Content -->
    <div x-show="Alpine.store('lmsView').mode === 'pdf'" x-cloak
         class="bg-white dark:bg-gray-800/50 rounded-xl border border-gray-200 dark:border-gray-700/60 p-6 text-center">
        <!-- Button logic from /app/planning/lms/monitor -->
        <a href="#" 
           @click.prevent="Alpine.store('lmsView').openPrintView()"
           class="inline-flex items-center gap-1.5 px-2.5 py-1.5 rounded-lg border border-teal-500/30 bg-teal-500/10 text-teal-400 transition-all duration-200 text-[10px] font-bold hover:bg-teal-500/20 inline-block mx-auto"
           title="Ver actividad en una página de impresión (Mermaid renderizado en el navegador)">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 002-2H5a2 2 0 002-2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4H7v4a2 2 0 002 2zm8-12V5a2 2 0 002-2H9a2 2 0 002 2v4h10z"></path>
            </svg>
            <span class="hidden sm:inline">Ver / Imprimir</span>
        </a>
        <p class="mt-4 text-sm text-gray-500 dark:text-gray-400">
            Haz clic en el botón acima para abrir una versión optimizada para impresión
            de esta actividad en una nueva pestaña.
        </p>
    </div>

    @if($this->celebrate)
    <div wire:ignore x-data="celebration()" x-init="run()" x-show="visible" role="status" aria-live="polite"
         class="fixed inset-0 z-[9999] pointer-events-none flex items-center justify-center px-4" x-cloak
         x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 scale-90"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95">
        <div class="relative text-center">
            <div aria-hidden="true" class="confetti-layer absolute inset-0 overflow-hidden">
                {{-- ~24 piezas generadas por x-init: spans absolutos con left/delay/duración/color/rotación inline; solo si NO prefers-reduced-motion --}}
            </div>
            <div class="px-6 py-6 rounded-2xl bg-white dark:bg-gray-800 border border-emerald-200 dark:border-emerald-500/30
                        shadow-xl shadow-emerald-500/10">
                @if($showMascot)
                    <x-lms.mascot :variant="'celebrate'" :size="'lg'" :emphasis="$mascotEmphasis" />
                @endif
                <p class="mt-4 text-2xl font-display font-bold text-gray-900 dark:text-white">¡Lo lograste! 🎉</p>
                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Has completado esta lección.</p>
            </div>
        </div>
    </div>
    @endif

    {{-- Estilos para Mermaid fullscreen / zoom toolbar --}}
    {{-- Sin @once — CSS es idempotente, y Livewire maneja re-renders sin duplicados problemáticos --}}
    <style>
        .mermaid-zoom-toolbar {
            backdrop-filter: blur(8px);
            -webkit-backdrop-filter: blur(8px);
        }
        [x-data="mermaidEmbed()"]:fullscreen {
            background: #f8fafc;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
            overflow: auto;
        }
        [x-data="mermaidEmbed()"]:fullscreen .mermaid-zoom-toolbar {
            opacity: 1 !important;
            position: fixed;
            top: 1rem;
            right: 1rem;
        }
        .zoom-act {
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        /* ── Mermaid fill height ── */
        .mermaid-fill-height {
            min-height: 0;
        }
        .mermaid-fill-height svg {
            width: 100% !important;
            flex: 1 !important;
        }
        /* Móvil: el viewBox nativo de Mermaid puede ser muy ancho (ej. 2212px).
           En ≤640px el JS (setupUI) establece width explícita a partir del viewBox
           (~60% ancho natural, máx. 840px) y max-width:none para que el diagrama
           quede legible con scroll horizontal. El wrapper mermaid-fill-height ya
           tiene overflow-x-auto (Tailwind class) que gestiona el scroll.
           Aquí solo nos aseguramos que el target (contenedor del SVG) permita
           propagar el overflow al wrapper padre. */
        .mermaid-fill-height > [x-ref="target"] {
            min-width: 100%;
        }

        /* ── lms-content: alto contraste en headings, tablas, blockquotes ── */
        .lms-content {
            font-size: 17px !important;
            line-height: 1.75 !important;
            color: #1e293b !important;
        }
        .lms-content :is(h1, h2, h3, h4) {
            color: #0f172a !important;
            font-weight: 700 !important;
            line-height: 1.3 !important;
            letter-spacing: -0.01em !important;
        }
        .lms-content h1 {
            font-size: 1.5em !important;
            margin: 1.2em 0 0.6em !important;
            padding-bottom: 0.3em !important;
            border-bottom: 2px solid #e2e8f0 !important;
        }
        .lms-content h2 {
            font-size: 1.25em !important;
            margin: 1.4em 0 0.5em !important;
            padding-bottom: 0.25em !important;
            border-bottom: 1.5px solid #CAE8BD !important;
            color: #4C7C3B !important;
        }
        .lms-content h3 {
            font-size: 1.05em !important;
            margin: 1.2em 0 0.4em !important;
            color: #1e293b !important;
        }
        .lms-content h4 {
            font-size: 0.95em !important;
            margin: 1em 0 0.3em !important;
            color: #334155 !important;
            font-weight: 600 !important;
        }
        .lms-content blockquote {
            border-left: 4px solid #B0DB9C !important;
            background: #ECFAE5 !important;
            padding: 0.75em 1.25em !important;
            margin: 1.2em 0 !important;
            border-radius: 0 0.5rem 0.5rem 0 !important;
        }
        .lms-content blockquote p {
            margin: 0 !important;
        }
        .lms-content table {
            width: 100% !important;
            border-collapse: collapse !important;
            margin: 1.2em 0 !important;
            font-size: 0.9em !important;
            line-height: 1.6 !important;
        }
        /* Móvil: tablas markdown anchas → scroll horizontal dentro de la
           tabla en lugar de desbordar la tarjeta. Se conserva el min-width
           legible y la tabla se vuelve un contenedor scrollable. */
        @media (max-width: 640px) {
            .lms-content table {
                display: block;
                overflow-x: auto;
                -webkit-overflow-scrolling: touch;
                white-space: nowrap;
                width: max-content;
                min-width: 100%;
                max-width: 100%;
            }
            .lms-content table thead,
            .lms-content table tbody,
            .lms-content table tr {
                display: table;
                width: 100%;
                table-layout: fixed;
            }
            .lms-content table th,
            .lms-content table td {
                white-space: normal;
            }
        }
        .lms-content table thead {
            border-bottom: 2px solid #B0DB9C !important;
        }
        .lms-content table th {
            background-color: #DDF6D2 !important;
            color: #0f172a !important;
            font-weight: 700 !important;
            font-size: 0.85em !important;
            text-transform: uppercase !important;
            letter-spacing: 0.025em !important;
            padding: 0.65rem 0.75rem !important;
            border: 1px solid #cbd5e1 !important;
            text-align: left !important;
            vertical-align: top !important;
        }
        .lms-content table td {
            padding: 0.55rem 0.75rem !important;
            border: 1px solid #cbd5e1 !important;
            vertical-align: top !important;
            color: #334155 !important;
        }
        .lms-content table tbody tr {
            border-bottom: 1px solid #e2e8f0 !important;
        }
        .lms-content table tbody tr:nth-child(even) {
            background-color: #f8fafc !important;
        }
        .lms-content table tbody tr:hover {
            background-color: #f1f5f9 !important;
        }
        .lms-content table :is(th, td) strong {
            color: inherit !important;
        }
        .lms-content p {
            margin: 0.6em 0 !important;
            line-height: 1.75 !important;
        }
        .lms-content p:first-child {
            margin-top: 0 !important;
        }
        .lms-content ul, .lms-content ol {
            margin: 0.6em 0 !important;
            padding-left: 1.4em !important;
        }
        .lms-content li {
            margin: 0.3em 0 !important;
            line-height: 1.7 !important;
            color: #1e293b !important;
        }
        .lms-content strong {
            color: #0f172a !important;
            font-weight: 700 !important;
        }
        .lms-content em {
            color: #334155 !important;
            font-style: italic !important;
        }
        .lms-content code {
            font-size: 0.875em !important;
            font-weight: 600 !important;
            padding: 0.15em 0.4em !important;
            border-radius: 4px !important;
            background: #f1f5f9 !important;
            color: #be123c !important;
            border: 1px solid #e2e8f0 !important;
        }
        .lms-content pre {
            background: #f8fafc !important;
            border: 1px solid #e2e8f0 !important;
            border-radius: 8px !important;
            padding: 1em !important;
            overflow-x: auto !important;
            margin: 1em 0 !important;
            font-size: 0.875em !important;
            line-height: 1.6 !important;
        }
        .lms-content pre code {
            background: none !important;
            border: none !important;
            padding: 0 !important;
            color: #1e293b !important;
            font-weight: 400 !important;
        }
        .lms-content img {
            max-width: 100% !important;
            height: auto !important;
            border-radius: 6px !important;
            margin: 1em 0 !important;
        }
        .lms-content a {
            color: #5E9849 !important;
            text-decoration: underline !important;
            text-underline-offset: 2px !important;
            text-decoration-thickness: 1px !important;
        }
        .lms-content a:hover {
            color: #4C7C3B !important;
            text-decoration-thickness: 2px !important;
        }
        .lms-content hr {
            border: none !important;
            border-top: 1.5px solid #e2e8f0 !important;
            margin: 1.5em 0 !important;
        }

        /* ── Diagramas SVG embebidos en contenido ── */
        .lms-svg-diagram svg {
            max-width: 100% !important;
            height: auto !important;
        }

        /* Móvil: el canvas SVG (viewBox 1000px+) no encoge hasta ilegible.
           El SVG escala al ancho del contenedor, pero si su ancho natural
           lo supera, se conserva legible con scroll horizontal dentro del
           contenedor (overflow-x-auto en .lms-svg-diagram). */
        .lms-svg-diagram {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
            touch-action: pan-x pan-y;
        }
        .lms-svg-diagram svg {
            min-width: max-content;
            margin: 0 auto;
        }
        @media (max-width: 480px) {
            .lms-svg-diagram svg {
                min-width: 480px;
            }
        }

        /* ── Anclas de sección: dejar espacio bajo el navbar sticky ── */
        [id^="seccion-"] {
            scroll-margin-top: 5.5rem;
        }

        /* ── Confeti para celebración C3 ── */
        @keyframes confetti-fall {
            0% {
                transform: translateY(-10vh) rotate(0deg);
                opacity: 0;
            }
            10% {
                opacity: 1;
            }
            100% {
                transform: translateY(100vh) rotate(360deg);
                opacity: 0;
            }
        }

        /* ── Movimiento reducido: respeta prefers-reduced-motion ── */
        @media (prefers-reduced-motion: reduce) {
            *, *::before, *::after {
                animation-duration: 0.01ms !important;
                animation-iteration-count: 1 !important;
                transition-duration: 0.01ms !important;
                scroll-behavior: auto !important;
            }

            .confetti-layer {
                display: none !important;
            }
        }

    </style>

    {{-- ═══════════════════════ READING NAV (progreso + scroll-spy) ═══════════════════════ --}}
    @once
        <script>
            document.addEventListener('alpine:init', function () {
                if (Alpine._readingNavRegistered) return;
                Alpine._readingNavRegistered = true;
                Alpine.data('readingNav', function () {
                    return {
                        progress: 0,
                        activeId: null,
                        init() {
                            this.updateProgress();
                            this.setupSpy();
                        },
                        destroy() {
                            // Limpiar listeners/observers al desmontar el componente
                            // (evita acumulación de handlers en re-renders Livewire).
                            if (this._spy) this._spy.disconnect();
                            if (this._onScroll) {
                                window.removeEventListener('scroll', this._onScroll, { passive: true });
                            }
                        },
                        updateProgress() {
                            const doc = document.documentElement;
                            const scrollTop = window.scrollY || doc.scrollTop;
                            const height = doc.scrollHeight - window.innerHeight;
                            this.progress = height > 0 ? Math.min(100, Math.round((scrollTop / height) * 100)) : 0;
                        },
                        // Fallback para navegadores sin IntersectionObserver: el
                        // mismo cálculo por scroll (comportamiento original).
                        updateActiveLegacy() {
                            const sections = Array.from(document.querySelectorAll('[id^="seccion-"]'));
                            const offset = 120;
                            let current = null;
                            for (const el of sections) {
                                if (el.getBoundingClientRect().top <= offset) {
                                    current = Number(el.id.replace('seccion-', ''));
                                }
                            }
                            this.activeId = current ?? (sections.length ? Number(sections[0].id.replace('seccion-', '')) : null);
                        },
                        setupSpy() {
                            const sections = Array.from(document.querySelectorAll('[id^="seccion-"]'));
                            if (!sections.length) {
                                this.activeId = null;
                                return;
                            }
                            // Valor inicial: primera sección (antes de que IO dispare).
                            this.activeId = Number(sections[0].id.replace('seccion-', ''));

                            if (!('IntersectionObserver' in window)) {
                                this._onScrollLegacy = () => this.updateActiveLegacy();
                                window.addEventListener('scroll', this._onScrollLegacy, { passive: true });
                                this.updateActiveLegacy();
                                return;
                            }

                            // Scroll-spy con IntersectionObserver: rootMargin negativo en la
                            // parte superior (120px) → una sección dispara isIntersecting
                            // justo cuando su tope pasa la línea de lectura, sin leer el DOM
                            // del scrollhandler en cada frame.
                            this._spy = new IntersectionObserver((entries) => {
                                for (const entry of entries) {
                                    if (entry.isIntersecting) {
                                        this.activeId = Number(entry.target.id.replace('seccion-', ''));
                                    }
                                }
                            }, { rootMargin: '-120px 0px 0px 0px', threshold: 0 });
                            sections.forEach((el) => this._spy.observe(el));

                            // Progreso: sigue necesitando scroll, pero es un cálculo O(1).
                            this._onScrollProgress = () => this.updateProgress();
                            window.addEventListener('scroll', this._onScrollProgress, { passive: true });
                            this.updateProgress();
                        },
                        goTo(id) {
                            const el = document.getElementById('seccion-' + id);
                            if (!el) return;
                            const reduce = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
                            el.scrollIntoView({ behavior: reduce ? 'auto' : 'smooth', block: 'start' });
                        },
                    };
                });

// Store lmsView: modo de lectura de la lección ('scroll' | 'pdf').
                // Vive en Alpine.store (fuera del DOM que diffea Livewire) → sobrevive a re-renders.
                Alpine.store('lmsView', {
                    mode: 'scroll',
                    set(v) {
                        this.mode = v;
                    },
                    openPrintView() {
                        // Abrir la vista de impresión en una nueva pestaña
                        const activityId = {{ $activity->id }};
                        const url = `/app/estudiante/activity/${activityId}/print`;
                        window.open(url, '_blank');
                    }
                });

                // Celebration component for C3
                if (Alpine._celebrationRegistered) return;
                Alpine._celebrationRegistered = true;
                Alpine.data('celebration', () => ({
                    visible: false,
                    run() {
                        // E2: sin confeti bajo prefers-reduced-motion; el mensaje igual se muestra
                        const reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
                        if (!reduceMotion) {
                            const confettiLayer = document.querySelector('.confetti-layer');
                            if (confettiLayer) {
                                const colors = ['#10b981', '#34d399', '#6ee7b7', '#fbbf24', '#f59e0b', '#f97316'];
                                const pieceCount = 24;

                                for (let i = 0; i < pieceCount; i++) {
                                    const piece = document.createElement('span');
                                    const size = Math.random() * 8 + 4; // 4-12px
                                    const left = Math.random() * 100; // 0-100%
                                    const delay = Math.random() * 3; // 0-3s
                                    const duration = Math.random() * 2 + 3; // 3-5s
                                    const rotation = Math.random() * 360; // 0-360deg
                                    const color = colors[Math.floor(Math.random() * colors.length)];

                                    piece.style.position = 'absolute';
                                    piece.style.width = size + 'px';
                                    piece.style.height = size + 'px';
                                    piece.style.backgroundColor = color;
                                    piece.style.borderRadius = '50%';
                                    piece.style.left = left + '%';
                                    piece.style.top = '-10%';
                                    piece.style.opacity = '0';
                                    piece.style.animation = `confetti-fall ${duration}s ease-out ${delay}s forwards`;
                                    piece.style.transform = `rotate(${rotation}deg)`;

                                    confettiLayer.appendChild(piece);
                                }
                            }
                        }

                        // Mostrar la tarjeta de celebración y auto-ocultarla tras 3.5s
                        this.visible = true;
                        setTimeout(() => { this.visible = false; }, 3500);
                    }
                }));
            });
        </script>
    @endonce
</div>
