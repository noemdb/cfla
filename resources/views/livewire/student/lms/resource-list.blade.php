<div class="max-w-4xl mx-auto py-8 px-4 space-y-6">
    @php
    // R4 · Color por materia (D2). Paleta literal con clases Tailwind — el JIT
    // escanea .blade.php bajo resources/, así que las clases concretas viven aquí
    // y la lógica de asignación en Asignatura::colorKey(). Misma clave → mismo
    // color en claro y oscuro, en todas las vistas del LMS.
    $__sc = [
        'sky' => [
            'dot'   => 'bg-sky-400',
            'badge' => 'bg-sky-100 text-sky-700 dark:bg-sky-500/10 dark:text-sky-300 border border-sky-300 dark:border-sky-500/30',
            'chip'  => 'bg-sky-500/10 text-sky-400',
            'text'  => 'text-sky-600 dark:text-sky-300',
        ],
        'emerald' => [
            'dot'   => 'bg-emerald-400',
            'badge' => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-500/10 dark:text-emerald-300 border border-emerald-300 dark:border-emerald-500/30',
            'chip'  => 'bg-emerald-500/10 text-emerald-400',
            'text'  => 'text-emerald-600 dark:text-emerald-300',
        ],
        'amber' => [
            'dot'   => 'bg-amber-400',
            'badge' => 'bg-amber-100 text-amber-700 dark:bg-amber-500/10 dark:text-amber-300 border border-amber-300 dark:border-amber-500/30',
            'chip'  => 'bg-amber-500/10 text-amber-400',
            'text'  => 'text-amber-600 dark:text-amber-300',
        ],
        'indigo' => [
            'dot'   => 'bg-indigo-400',
            'badge' => 'bg-indigo-100 text-indigo-700 dark:bg-indigo-500/10 dark:text-indigo-300 border border-indigo-300 dark:border-indigo-500/30',
            'chip'  => 'bg-indigo-500/10 text-indigo-400',
            'text'  => 'text-indigo-600 dark:text-indigo-300',
        ],
        'purple' => [
            'dot'   => 'bg-purple-400',
            'badge' => 'bg-purple-100 text-purple-700 dark:bg-purple-500/10 dark:text-purple-300 border border-purple-300 dark:border-purple-500/30',
            'chip'  => 'bg-purple-500/10 text-purple-400',
            'text'  => 'text-purple-600 dark:text-purple-300',
        ],
        'orange' => [
            'dot'   => 'bg-orange-400',
            'badge' => 'bg-orange-100 text-orange-700 dark:bg-orange-500/10 dark:text-orange-300 border border-orange-300 dark:border-orange-500/30',
            'chip'  => 'bg-orange-500/10 text-orange-400',
            'text'  => 'text-orange-600 dark:text-orange-300',
        ],
        'rose' => [
            'dot'   => 'bg-rose-400',
            'badge' => 'bg-rose-100 text-rose-700 dark:bg-rose-500/10 dark:text-rose-300 border border-rose-300 dark:border-rose-500/30',
            'chip'  => 'bg-rose-500/10 text-rose-400',
            'text'  => 'text-rose-600 dark:text-rose-300',
        ],
        'teal' => [
            'dot'   => 'bg-teal-400',
            'badge' => 'bg-teal-100 text-teal-700 dark:bg-teal-500/10 dark:text-teal-300 border border-teal-300 dark:border-teal-500/30',
            'chip'  => 'bg-teal-500/10 text-teal-400',
            'text'  => 'text-teal-600 dark:text-teal-300',
        ],
        'slate' => [
            'dot'   => 'bg-slate-400',
            'badge' => 'bg-slate-100 text-slate-700 dark:bg-slate-500/10 dark:text-slate-300 border border-slate-300 dark:border-slate-500/30',
            'chip'  => 'bg-slate-500/10 text-slate-400',
            'text'  => 'text-slate-600 dark:text-slate-300',
        ],
    ];
    $__scKey = static fn (?string $name): string => \App\Models\app\Academy\Asignatura::colorKey($name);
    @endphp

    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-lg font-bold text-gray-900 dark:text-white">Recursos Compartidos</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                Material descargable de tus actividades
            </p>
        </div>
    </div>

    {{-- R3/R6 · Filtros con aria-label y foco visible (E1) + icono de búsqueda --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
        <div class="relative">
            <svg class="pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 dark:text-gray-500" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
            <input type="text" wire:model.live.debounce.300ms="search"
                   placeholder="Buscar recurso o actividad…"
                   aria-label="Buscar recurso o actividad"
                   class="w-full bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg pl-9 pr-4 py-2 text-sm focus:outline-none focus-visible:ring-2 ring-emerald-500/50 focus-visible:ring-offset-2 focus-visible:border-emerald-500/40 dark:focus-visible:ring-offset-gray-900"/>
        </div>
        <select wire:model.live="lapsoId"
                aria-label="Filtrar por lapso"
                class="w-full bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg px-4 py-2 text-sm focus:outline-none focus-visible:ring-2 ring-emerald-500/50 focus-visible:ring-offset-2 focus-visible:border-emerald-500/40 dark:focus-visible:ring-offset-gray-900">
            <option value="">Todos los lapsos</option>
            @foreach($lapsos as $id => $name)
                <option value="{{ $id }}">{{ $name }}</option>
            @endforeach
        </select>
        <select wire:model.live="typeFilter"
                wire:changing="resetPage"
                aria-label="Filtrar por tipo de recurso"
                class="w-full bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg px-4 py-2 text-sm focus:outline-none focus-visible:ring-2 ring-emerald-500/50 focus-visible:ring-offset-2 focus-visible:border-emerald-500/40 dark:focus-visible:ring-offset-gray-900">
            <option value="">Todos los tipos</option>
            <option value="downloadable">Descargable</option>
            <option value="external">Enlace externo</option>
            <option value="pdf">PDF</option>
            <option value="image">Imagen</option>
            <option value="video">Video</option>
            <option value="audio">Audio</option>
        </select>
        <select wire:model.live="asignaturaId"
                wire:changing="resetPage"
                aria-label="Filtrar por asignatura"
                class="w-full bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg px-4 py-2 text-sm focus:outline-none focus-visible:ring-2 ring-emerald-500/50 focus-visible:ring-offset-2 focus-visible:border-emerald-500/40 dark:focus-visible:ring-offset-gray-900">
            <option value="">Todas las materias</option>
            @foreach($asignaturas as $id => $name)
                <option value="{{ $id }}">{{ $name }}</option>
            @endforeach
        </select>
    </div>

    {{-- R1 · Skeleton de carga (patrón G2): mismo target scoped que la grilla real,
         aria-hidden, filas con la silueta de la tarjeta (icono + 2 líneas + pie). --}}
    <div wire:loading.delay.shorter
         wire:target="search, lapsoId, typeFilter, asignaturaId, gotoPage"
         aria-hidden="true"
         class="grid grid-cols-1 sm:grid-cols-2 gap-5">
        @for($i = 0; $i < 3; $i++)
            <div class="w-full h-full bg-white dark:bg-gray-800/50 border border-gray-200 dark:border-gray-700 rounded-xl p-5 space-y-4">
                <div class="flex items-start gap-3.5">
                    <span class="w-12 h-12 rounded-lg bg-gray-100 dark:bg-gray-700/60 animate-pulse shrink-0"></span>
                    <span class="min-w-0 flex-1 space-y-2.5">
                        <span class="block h-4 w-3/4 rounded bg-gray-100 dark:bg-gray-700/60 animate-pulse"></span>
                        <span class="block h-3.5 w-1/2 rounded bg-gray-100 dark:bg-gray-700/60 animate-pulse"></span>
                    </span>
                </div>
                <div class="flex items-center justify-between pt-2.5 border-t border-gray-100 dark:border-gray-700">
                    <span class="h-4.5 w-24 rounded-full bg-gray-100 dark:bg-gray-700/60 animate-pulse"></span>
                    <span class="h-5 w-14 rounded-full bg-gray-100 dark:bg-gray-700/60 animate-pulse"></span>
                </div>
                <div class="flex items-center justify-between pt-1.5">
                    <span class="h-7 w-20 rounded-lg bg-gray-100 dark:bg-gray-700/60 animate-pulse"></span>
                    <span class="h-7 w-20 rounded-lg bg-gray-100 dark:bg-gray-700/60 animate-pulse"></span>
                </div>
            </div>
        @endfor
    </div>

    {{-- Grid de recursos --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5"
         wire:loading.remove
         wire:target="search, lapsoId, typeFilter, asignaturaId, gotoPage">
        @forelse($resources as $resource)
            @php
                $isLink = ($resource->_type ?? '') === 'link';
                $isEmbed = ($resource->_type ?? '') === 'embed';
                $subject = $resource->activity?->pevaluacion?->pensum?->asignatura?->name ?? '';
                $scKey = $__scKey($subject);
                $mime = $resource->media?->mime_type ?? '';
                $typeKey = match (true) {
                    $isLink => 'link',
                    $isEmbed => 'embed',
                    str_starts_with($mime, 'image/') => 'image',
                    $mime === 'application/pdf' => 'pdf',
                    str_starts_with($mime, 'video/') => 'video',
                    str_starts_with($mime, 'audio/') => 'audio',
                    default => 'file',
                };
                $typeLabel = ['pdf' => 'PDF', 'image' => 'Imagen', 'video' => 'Video', 'audio' => 'Audio', 'file' => 'Archivo', 'link' => '', 'embed' => 'Insertar'][$typeKey] ?? ($resource->link_type ?? 'Enlace');
                $typeChip = [
                    'pdf'   => 'bg-rose-100 text-rose-700 dark:bg-rose-500/10 dark:text-rose-300 border border-rose-300 dark:border-rose-500/30',
                    'image' => 'bg-sky-100 text-sky-700 dark:bg-sky-500/10 dark:text-sky-300 border border-sky-300 dark:border-sky-500/30',
                    'video' => 'bg-purple-100 text-purple-700 dark:bg-purple-500/10 dark:text-purple-300 border border-purple-300 dark:border-purple-500/30',
                    'audio' => 'bg-amber-100 text-amber-700 dark:bg-amber-500/10 dark:text-amber-300 border border-amber-300 dark:border-amber-500/30',
                    'file'  => 'bg-slate-100 text-slate-700 dark:bg-slate-500/10 dark:text-slate-300 border border-slate-300 dark:border-slate-500/30',
                    'link'  => 'bg-blue-100 text-blue-700 dark:bg-blue-500/10 dark:text-blue-300 border border-blue-300 dark:border-blue-500/30',
                    'embed' => 'bg-indigo-100 text-indigo-700 dark:bg-indigo-500/10 dark:text-indigo-300 border border-indigo-300 dark:border-indigo-500/30',
                ][$typeKey];
            @endphp
            <article class="group w-full h-full bg-white dark:bg-gray-800/50 border border-gray-200 dark:border-gray-700 rounded-xl p-5 space-y-4 hover:border-emerald-500/30 hover:-translate-y-0.5 hover:shadow-md hover:shadow-gray-900/5 dark:hover:shadow-black/20 transition-all duration-200 ease-out">
                <div class="flex items-start gap-3.5">
                    <div class="w-12 h-12 rounded-lg {{ $__sc[$scKey]['chip'] }} flex items-center justify-center shrink-0">
                        @if($typeKey === 'image')
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                        @elseif($typeKey === 'video')
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                            </svg>
                        @elseif($typeKey === 'audio')
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm12-3c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2z"/>
                            </svg>
                        @elseif($typeKey === 'pdf')
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                        @elseif($typeKey === 'embed')
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                            </svg>
                        @else
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                            </svg>
                        @endif
                    </div>
                    <div class="min-w-0 flex-1">
                        <p class="text-base font-semibold text-gray-900 dark:text-white truncate">
                            {{ $resource->display_name }}
                        </p>
                        <p class="text-[13px] text-gray-500 dark:text-gray-400 mt-0.5 truncate">
                            {{ $resource->activity?->topic ?? '—' }}
                        </p>
                    </div>
                </div>
                @if($resource->description)
                    <p class="text-xs text-gray-500 dark:text-gray-400 leading-relaxed line-clamp-2">
                        {{ $resource->description }}
                    </p>
                @endif
                @if($resource->activity?->lmsPublication)
                    @php
                        $__act = $resource->activity;
                        $__lmsPub = $__act->lmsPublication;
                        $__sectionCount = $__act->lmsSections?->count() ?? 0;
                        $__lessonCount = 1;
                    @endphp
                    <div class="mt-3 pt-3 border-t border-gray-100 dark:border-gray-700">
                        <div class="flex items-center justify-between mb-2">
                            <span class="inline-flex items-center gap-1.5 text-[10px] font-bold uppercase tracking-widest text-gray-500 dark:text-gray-400">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                                </svg>
                                Lección (LMS)
                            </span>
                            <a href="{{ route('student.lms.activity', $__act) }}"
                               class="inline-flex items-center gap-1 text-[10px] font-bold uppercase tracking-wider text-sky-600 dark:text-sky-400 hover:text-sky-700 dark:hover:text-sky-300 transition-colors">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                                Ver lección
                            </a>
                        </div>
                        <div class="flex items-center gap-2 flex-wrap">
                            @if($__lmsPub->status === 'PUBLISHED')
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 bg-emerald-100 dark:bg-emerald-500/10 text-emerald-700 dark:text-emerald-400 text-[10px] font-bold rounded-md border border-emerald-200 dark:border-emerald-500/20">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                    Publicada
                                </span>
                            @elseif($__lmsPub->status === 'SCHEDULED')
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 bg-amber-100 dark:bg-amber-500/10 text-amber-700 dark:text-amber-400 text-[10px] font-bold rounded-md border border-amber-200 dark:border-amber-500/20">
                                    <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                                    Programada
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 bg-gray-100 dark:bg-white/5 text-gray-500 dark:text-gray-400 text-[10px] font-bold rounded-md border border-gray-200 dark:border-white/5">
                                    <span class="w-1.5 h-1.5 rounded-full bg-gray-400"></span>
                                    Borrador
                                </span>
                            @endif
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 bg-sky-100 dark:bg-sky-500/10 text-sky-700 dark:text-sky-400 text-[10px] font-bold rounded-md border border-sky-200 dark:border-sky-500/20">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                                </svg>
                                {{ $__sectionCount }} {{ $__sectionCount === 1 ? 'sección' : 'secciones' }}
                            </span>
                            <span class="px-2.5 py-1 bg-violet-100 dark:bg-violet-500/10 text-violet-700 dark:text-violet-400 text-[10px] font-bold rounded-lg border border-violet-200 dark:border-violet-500/20">
                                <svg class="w-3 h-3 inline -mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                                </svg>
                                {{ $__lessonCount }} {{ $__lessonCount === 1 ? 'lección' : 'lecciones' }}
                            </span>
                        </div>
                    </div>
                @endif
                <div class="flex items-center justify-between gap-2 pt-2.5 border-t border-gray-100 dark:border-gray-700">
                    <span class="min-w-0 inline-flex items-center gap-1.5">
                        @if($subject)
                            <span class="w-2 h-2 rounded-full {{ $__sc[$scKey]['dot'] }} shrink-0" aria-hidden="true"></span>
                            <span class="truncate text-xs font-semibold {{ $__sc[$scKey]['text'] }}">{{ $subject }}</span>
                        @endif
                    </span>
                    <span class="shrink-0 inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold uppercase tracking-wider {{ $typeChip }}">
                        {{ $typeLabel }}
                    </span>
                </div>
                <div class="flex items-center justify-between gap-2 pt-1.5">
                    @if($isLink)
                    <a href="{{ $resource->url }}" target="_blank" rel="noopener noreferrer"
                       class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-lg text-xs font-bold text-white bg-blue-600 hover:bg-blue-500 shadow-sm transition-all focus-visible:ring-2 ring-emerald-500/50 focus-visible:ring-offset-2 dark:focus-visible:ring-offset-gray-900">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                        </svg>
                        Abrir enlace
                    </a>
                    <button type="button"
                            wire:click="preview({{ $resource->id }})"
                            data-preview-trigger-{{ $resource->id }}
                            class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-medium text-gray-500 dark:text-gray-400 hover:text-sky-400 bg-transparent hover:bg-sky-500/10 border border-gray-200 dark:border-gray-600 hover:border-sky-500/20 transition-all focus-visible:ring-2 ring-emerald-500/50 focus-visible:ring-offset-2 dark:focus-visible:ring-offset-gray-900"
                            title="Vista previa">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                        Vista previa
                    </button>
                    @elseif($resource->_type === 'embed')
                    <button type="button"
                            wire:click="preview({{ $resource->id }}, 'embed')"
                            data-preview-trigger-{{ $resource->id }}
                            class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-medium text-gray-500 dark:text-gray-400 hover:text-sky-400 bg-transparent hover:bg-sky-500/10 border border-gray-200 dark:border-gray-600 hover:border-sky-500/20 transition-all focus-visible:ring-2 ring-emerald-500/50 focus-visible:ring-offset-2 dark:focus-visible:ring-offset-gray-900"
                            title="Vista previa">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                        Vista previa
                    </button>
                    @else
                    <button type="button"
                            wire:click="preview({{ $resource->id }})"
                            data-preview-trigger-{{ $resource->id }}
                            class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-medium text-gray-500 dark:text-gray-400 hover:text-sky-400 bg-transparent hover:bg-sky-500/10 border border-gray-200 dark:border-gray-600 hover:border-sky-500/20 transition-all focus-visible:ring-2 ring-emerald-500/50 focus-visible:ring-offset-2 dark:focus-visible:ring-offset-gray-900"
                            title="Vista previa">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                        Vista previa
                    </button>
                    @if(!$isEmbed)
                    <a href="{{ route('student.lms.resource.download', $resource->id) }}"
                       target="_blank" rel="noopener noreferrer"
                       class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-lg text-xs font-bold text-white bg-emerald-600 hover:bg-emerald-500 shadow-sm transition-all focus-visible:ring-2 ring-emerald-500/50 focus-visible:ring-offset-2 dark:focus-visible:ring-offset-gray-900">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                        </svg>
                        Descargar
                    </a>
                    @endif
                    @endif
                </div>
            </article>
        @empty
            {{-- R2 · Empty state ilustrado (patrón C5): mascota (solo ≤12), mensaje
                 contextual según filtros, micro-copia y CTAs. --}}
            <section class="col-span-full text-center py-14 px-4 rounded-xl border border-dashed border-gray-200 dark:border-gray-700" aria-live="polite">
                @if($showMascot)
                    <div class="flex justify-center mb-3">
                        <x-lms.mascot :variant="'idle'" :size="'sm'" :emphasis="$mascotEmphasis" />
                    </div>
                @endif
                <p class="text-sm text-gray-600 dark:text-gray-400">
                    @if($search !== '' && $lapsoId !== '')
                        No encontramos recursos para “<span class="font-semibold text-gray-700 dark:text-gray-300">{{ $search }}</span>” en {{ $lapsos[$lapsoId] ?? 'el lapso seleccionado' }}.
                    @elseif($search !== '')
                        No encontramos recursos para “<span class="font-semibold text-gray-700 dark:text-gray-300">{{ $search }}</span>”.
                    @elseif($lapsoId !== '')
                        No hay recursos en {{ $lapsos[$lapsoId] ?? 'este lapso' }}.
                    @else
                        Aún no hay recursos compartidos.
                    @endif
                </p>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Prueba con otra búsqueda o cambia el lapso.</p>
                <div class="mt-4 flex flex-wrap items-center justify-center gap-2">
                    @if($search !== '')
                        <button type="button"
                                wire:click="$set('search', ''); $set('typeFilter', ''); $set('asignaturaId', '')"
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
                        Ver todos
                    </button>
                </div>
            </section>
        @endforelse
    </div>

    @if($resources->hasPages())
        <div class="pt-4">{{ $resources->links('vendor.livewire.custom-tailwind') }}</div>
    @endif

    {{-- ═══ PREVIEW MODAL ═══ --}}
    @if($showPreviewModal && $previewResource)
        @php
            $r = $previewResource;
            $media = $r['media'] ?? [];
            $mime = $media['mime_type'] ?? '';
            $isImage = str_starts_with($mime, 'image/');
            $isPdf = $mime === 'application/pdf';
            $isVideo = str_starts_with($mime, 'video/');
            $dataUrl = $media['public_url'] ?? '';
        @endphp

        {{-- R5 · Modal accesible (patrón <dialog> + ::backdrop): role=dialog,
             aria-modal, aria-labelledby, Escape cierra y el foco vuelve al
             botón que abrió (data-preview-trigger-{id}). --}}
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4"
             x-data x-cloak
             x-init="$nextTick(() => $el.querySelector('[data-preview-close]')?.focus())"
             @keydown.escape.window="$wire.closePreview().then(() => document.querySelector('[data-preview-trigger-{{ $r['id'] }}]')?.focus())">
            {{-- Backdrop (scrim) --}}
            <div class="backdrop fixed inset-0 bg-black/60 backdrop-blur-sm" @click="$wire.closePreview()"></div>

            {{-- Modal panel --}}
            <div role="dialog" aria-modal="true" aria-labelledby="preview-title"
                 class="relative bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 shadow-2xl w-full max-w-4xl max-h-[90vh] flex flex-col overflow-hidden z-10">
                {{-- Header --}}
                <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100 dark:border-gray-700 shrink-0">
                    <div class="min-w-0 flex-1">
                        <h2 id="preview-title" class="text-base font-bold text-gray-900 dark:text-white truncate pr-4">
                            {{ $r['display_name'] ?? 'Recurso' }}
                        </h2>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5 truncate">
                            {{ $r['activity']['topic'] ?? '' }}
                            @if($r['description'])
                                · {{ $r['description'] }}
                            @endif
                        </p>
                    </div>
                    <button type="button" data-preview-close
                            @click="$wire.closePreview().then(() => document.querySelector('[data-preview-trigger-{{ $r['id'] }}]')?.focus())"
                            class="shrink-0 p-1.5 rounded-lg text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors focus-visible:ring-2 ring-emerald-500/50 focus-visible:ring-offset-2 dark:focus-visible:ring-offset-gray-800">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                        <span class="sr-only">Cerrar vista previa</span>
                    </button>
                </div>

                {{-- Body: preview area --}}
                <div class="flex-1 overflow-auto p-6 bg-gray-50/50 dark:bg-gray-900/30">
                    @if($isImage && $dataUrl)
                        <div class="flex items-center justify-center min-h-[200px]">
                            <img src="{{ $dataUrl }}"
                                 alt="{{ $r['display_name'] ?? 'Preview' }}"
                                 class="max-w-full max-h-[60vh] rounded-lg shadow-lg object-contain"
                                 onerror="this.closest('.flex').innerHTML = '<p class=\'text-sm text-red-400\'>No se pudo cargar la vista previa de la imagen.</p>'">
                        </div>
                    @elseif($isPdf && $dataUrl)
                        <div class="w-full" style="height: 65vh;">
                            <iframe src="{{ $dataUrl }}"
                                    class="w-full h-full rounded-lg border border-gray-200 dark:border-gray-700 bg-white"
                                    title="Vista previa del PDF"
                                    onerror="this.outerHTML = '<p class=\'text-sm text-red-400\'>No se pudo cargar la vista previa del PDF.</p>'">
                            </iframe>
                        </div>
                    @elseif($isVideo && $dataUrl)
                        <div class="flex items-center justify-center min-h-[200px]">
                            <video controls class="max-w-full max-h-[60vh] rounded-lg shadow-lg"
                                   onerror="this.outerHTML = '<p class=\'text-sm text-red-400\'>No se pudo cargar el video.</p>'">
                                <source src="{{ $dataUrl }}" type="{{ $mime }}">
                            </video>
                        </div>
                    @else
                        {{-- Fallback: file info card --}}
                        <div class="flex flex-col items-center justify-center py-16 text-center">
                            <div class="w-16 h-16 rounded-2xl bg-emerald-500/10 flex items-center justify-center mb-4">
                                <svg class="w-8 h-8 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                                </svg>
                            </div>
                            <p class="text-sm font-medium text-gray-700 dark:text-gray-300">
                                Vista previa no disponible para este tipo de archivo
                            </p>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                @if($mime)
                                    {{ $mime }}
                                @else
                                    Tipo de archivo desconocido
                                @endif
                            </p>
                            @if($media['size_for_humans'] ?? false)
                                <p class="text-xs text-gray-400 mt-3">
                                    Tamaño: {{ $media['size_for_humans'] }}
                                </p>
                            @endif
                        </div>
                    @endif
                </div>

                {{-- Footer --}}
                <div class="flex items-center justify-between px-6 py-3 border-t border-gray-100 dark:border-gray-700 bg-gray-50/80 dark:bg-gray-800/80 shrink-0">
                    <span class="text-xs text-gray-500 dark:text-gray-400">
                        @if($media['size_for_humans'] ?? false)
                            {{ $media['original_name'] ?? '' }} · {{ $media['size_for_humans'] }}
                        @else
                            {{ $media['original_name'] ?? '' }}
                        @endif
                    </span>
                    @if(!isset($r['_type']) || $r['_type'] !== 'embed')
                        <a href="{{ route('student.lms.resource.download', $r['id']) }}"
                           target="_blank" rel="noopener noreferrer"
                           class="inline-flex items-center gap-1.5 px-4 py-1.5 rounded-lg text-xs font-bold text-emerald-400 hover:text-emerald-300 bg-emerald-500/10 hover:bg-emerald-500/20 border border-emerald-500/20 transition-all focus-visible:ring-2 ring-emerald-500/50 focus-visible:ring-offset-2 dark:focus-visible:ring-offset-gray-800">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                            </svg>
                            Descargar
                        </a>
                    @endif
                </div>
            </div>
        </div>
    @endif

    {{-- ═══ EMBED PREVIEW MODAL (HTML embebido) ═══ --}}
    @if($showEmbedPreviewModal && $embedPreview)
        @php $e = $embedPreview; @endphp

        {{-- Modal especial para recursos del tipo html (embebido): renderiza el
             contenido real (html_content) en un diálogo propio, con el mismo
             patrón accesible R5 (role=dialog, Escape, retorno de foco). --}}
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4"
             x-data x-cloak
             x-init="$nextTick(() => $el.querySelector('[data-embed-preview-close]')?.focus())"
             @keydown.escape.window="$wire.closeEmbedPreview().then(() => document.querySelector('[data-preview-trigger-{{ $e['id'] }}]')?.focus())">
            {{-- Backdrop (scrim) --}}
            <div class="backdrop fixed inset-0 bg-black/60 backdrop-blur-sm" @click="$wire.closeEmbedPreview()"></div>

            {{-- Modal panel --}}
            <div role="dialog" aria-modal="true" aria-labelledby="embed-preview-title"
                 class="relative bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 shadow-2xl w-full max-w-3xl max-h-[90vh] flex flex-col overflow-hidden z-10">
                {{-- Header --}}
                <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100 dark:border-gray-700 shrink-0">
                    <div class="min-w-0 flex-1">
                        <h2 id="embed-preview-title" class="text-base font-bold text-gray-900 dark:text-white truncate pr-4">
                            {{ $e['title'] ?? 'Contenido embebido' }}
                        </h2>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5 truncate">
                            {{ $e['activity']['topic'] ?? '' }}
                            @if(!empty($e['activity']['pevaluacion']['pensum']['asignatura']['name']))
                                · {{ $e['activity']['pevaluacion']['pensum']['asignatura']['name'] }}
                            @endif
                        </p>
                    </div>
                    <button type="button" data-embed-preview-close
                            @click="$wire.closeEmbedPreview().then(() => document.querySelector('[data-preview-trigger-{{ $e['id'] }}]')?.focus())"
                            class="shrink-0 p-1.5 rounded-lg text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors focus-visible:ring-2 ring-emerald-500/50 focus-visible:ring-offset-2 dark:focus-visible:ring-offset-gray-800">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                        <span class="sr-only">Cerrar vista previa</span>
                    </button>
                </div>

                {{-- Body: render del HTML embebido --}}
                <div class="flex-1 overflow-auto p-6 bg-gray-50/50 dark:bg-gray-900/30">
                    @if(!empty($e['html_content']))
                        <div class="w-full max-w-full overflow-x-auto">
                            {!! $e['html_content'] !!}
                        </div>
                    @else
                        <div class="flex flex-col items-center justify-center py-16 text-center">
                            <div class="w-16 h-16 rounded-2xl bg-sky-500/10 flex items-center justify-center mb-4">
                                <svg class="w-8 h-8 text-sky-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                                </svg>
                            </div>
                            <p class="text-sm font-medium text-gray-700 dark:text-gray-300">
                                Este contenido no tiene HTML embebido
                            </p>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                El recurso no incluye contenido para mostrar en la vista previa.
                            </p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    @endif
</div>
