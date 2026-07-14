<div class="max-w-4xl mx-auto py-10 px-4 sm:px-6 space-y-8">

    {{-- ── Navegación superior ── --}}
    <div class="flex items-center justify-between">
        <a href="{{ route('app.planning.lms.monitor') }}"
           class="group inline-flex items-center gap-1.5 text-xs font-medium text-slate-500 hover:text-emerald-400 transition-colors duration-200">
            <svg class="w-4 h-4 transition-transform duration-200 group-hover:-translate-x-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            Volver al monitor
        </a>

        {{-- Acciones rápidas (futuro) --}}
    </div>

    {{-- ── Encabezado de la lección ── --}}
    <div class="bg-white/5 backdrop-blur-sm rounded-lg border border-white/10 p-6 sm:p-8">
        <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-3">
            <div class="space-y-3 min-w-0">
                <div class="flex items-center gap-2">
                    <span class="w-1 h-6 bg-emerald-500 rounded-full shrink-0"></span>
                    <h1 class="text-lg sm:text-lg font-bold text-white leading-tight">Vista de Lección</h1>
                </div>
                <p class="text-sm text-slate-400 leading-relaxed">
                    {{ $activity->topic ?? 'Actividad sin título' }}
                </p>
            </div>
            <div class="shrink-0 text-right">
                <div class="text-xs font-medium text-slate-500">
                    {{ $activity->pevaluacion?->pensum?->asignatura?->name ?? '' }}
                </div>
                <div class="text-xs text-slate-600 mt-0.5">
                    {{ $activity->pevaluacion?->pensum?->grado?->name ?? '' }}
                    {{ $activity->pevaluacion?->seccion?->name ?? '' }}
                </div>
            </div>
        </div>
    </div>

    {{-- ── Estado de publicación ── --}}
    @if($publication)
        <div class="flex flex-wrap items-center gap-x-4 gap-y-2 px-5 py-2 rounded-lg
                    bg-slate-800/30 border border-slate-700/40">
            <div class="flex items-center gap-2">
                <span @class([
                    'inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-semibold tracking-wide',
                    'bg-emerald-500/15 text-emerald-400 border border-emerald-500/20' => $publication->status === 'PUBLISHED',
                    'bg-amber-500/15 text-amber-400 border border-amber-500/20'     => $publication->status === 'SCHEDULED',
                    'bg-slate-500/15 text-slate-400 border border-slate-500/20'     => $publication->status === 'DRAFT',
                    'bg-red-500/15 text-red-400 border border-red-500/20'           => $publication->status === 'ARCHIVED',
                ])>
                    @if($publication->status === 'PUBLISHED')
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    @elseif($publication->status === 'SCHEDULED')
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    @elseif($publication->status === 'ARCHIVED')
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/></svg>
                    @else
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                    @endif
                    {{ match($publication->status) {
                        'PUBLISHED' => 'Publicado',
                        'SCHEDULED' => 'Programado',
                        'ARCHIVED'  => 'Archivado',
                        default     => 'Borrador',
                    } }}
                </span>
            </div>

            <div class="flex items-center gap-3 text-xs text-slate-500">
                @if($publication->published_at)
                    <span class="flex items-center gap-1.5">
                        <svg class="w-3.5 h-3.5 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        {{ $publication->published_at->format('d/m/Y H:i') }}
                    </span>
                @elseif($publication->publish_at)
                    <span class="flex items-center gap-1.5">
                        <svg class="w-3.5 h-3.5 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        {{ \Carbon\Carbon::parse($publication->publish_at)->format('d/m/Y H:i') }}
                    </span>
                @else
                    <span class="text-slate-600">Sin publicar</span>
                @endif

                <span class="text-slate-700">·</span>

                <span class="flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                    {{ $publication->publisher?->name ?? '—' }}
                </span>
            </div>
        </div>
    @endif

    {{-- ── Secciones del contenido ── --}}
    @forelse($sections as $section)
        <section class="bg-white rounded-lg shadow-lg shadow-black/5 overflow-hidden border border-slate-200/80">
            {{-- Encabezado de sección con barra superior esmeralda --}}
            <div class="relative px-6 py-5 border-b border-slate-100">
                <span class="absolute inset-x-0 top-0 h-1 bg-gradient-to-r from-emerald-500 to-emerald-400"></span>
                <div class="flex items-center gap-3">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 shrink-0"></span>
                    <h2 class="text-base font-bold text-slate-900">{{ $section->title }}</h2>
                </div>
            </div>

            {{-- Contenidos --}}
            <div class="px-6 py-5 space-y-6">
                @forelse($section->visibleContents as $content)
                    <div class="space-y-2 @if(!$loop->first) pt-2 border-t border-slate-50 @endif">
                        @if($content->title)
                            <div class="flex items-start gap-2.5">
                                <span class="w-0.5 h-5 bg-emerald-400 rounded-full mt-1.5 shrink-0"></span>
                                <div>
                                    <h3 class="text-sm font-bold text-slate-800 leading-snug">{{ $content->title }}</h3>
                                </div>
                            </div>
                        @endif
                        <div class="@if($content->title) ml-5 @endif">
                            <x-lms-content-renderer :body="$content->body ?? ''" :sanitize="false" />
                        </div>
                    </div>
                @empty
                    <div class="flex flex-col items-center py-8 text-center">
                        <svg class="w-10 h-10 text-slate-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                        </svg>
                        <p class="text-sm text-slate-400">Sin contenido en esta sección.</p>
                    </div>
                @endforelse
            </div>
        </section>
    @empty
        {{-- Empty state mejorado --}}
        <div class="text-center py-20 bg-white/5 rounded-lg border border-dashed border-white/10">
            <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-slate-800/50 mb-2">
                <svg class="w-8 h-8 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                </svg>
            </div>
            <p class="text-base font-medium text-slate-400">Esta lección no tiene secciones visibles</p>
            <p class="text-sm text-slate-600 mt-1 max-w-xs mx-auto">
                El profesor debe agregar contenido antes de publicar la lección para los estudiantes.
            </p>
        </div>
    @endforelse

    {{-- ── Recursos descargables ── --}}
    @if($resources->isNotEmpty())
        <div class="rounded-lg border border-blue-500/10 bg-blue-500/5 p-6">
            <h3 class="text-xs font-bold text-blue-400 uppercase tracking-widest mb-2 flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                </svg>
                Recursos descargables
            </h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                @foreach($resources as $res)
                    <div class="group flex items-center gap-3 p-3.5 rounded-lg bg-slate-800/60 border border-slate-700/40
                                hover:bg-slate-700/60 hover:border-slate-600/50 transition-all duration-200 cursor-pointer">
                        <div class="w-10 h-10 rounded-lg bg-blue-500/10 flex items-center justify-center
                                    group-hover:bg-blue-500/20 transition-colors duration-200">
                            <svg class="w-5 h-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                            </svg>
                        </div>
                        <div class="min-w-0 flex-1">
                            <p class="text-sm font-medium text-slate-200 truncate group-hover:text-white transition-colors">
                                {{ $res->display_name }}
                            </p>
                            @if($res->media)
                                <p class="text-xs text-slate-500 mt-0.5">{{ $res->media->size_for_humans ?? '' }}</p>
                            @endif
                        </div>
                        <svg class="w-4 h-4 text-slate-600 group-hover:text-slate-400 transition-colors shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    {{-- ── Enlaces de interés ── --}}
    @if($links->isNotEmpty())
        <div class="rounded-lg border border-emerald-500/10 bg-emerald-500/5 p-6">
            <h3 class="text-xs font-bold text-emerald-400 uppercase tracking-widest mb-2 flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/>
                </svg>
                Enlaces de interés
            </h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                @foreach($links as $link)
                    <a href="{{ $link->url }}" target="_blank" rel="noopener noreferrer"
                       class="group flex items-center gap-3 p-3.5 rounded-lg bg-slate-800/60 border border-slate-700/40
                              hover:bg-slate-700/60 hover:border-slate-600/50 transition-all duration-200">
                        <div class="w-10 h-10 rounded-lg bg-emerald-500/10 flex items-center justify-center
                                    group-hover:bg-emerald-500/20 transition-colors duration-200">
                            <svg class="w-5 h-5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                            </svg>
                        </div>
                        <div class="min-w-0 flex-1">
                            <p class="text-sm font-medium text-slate-200 truncate group-hover:text-white transition-colors">
                                {{ $link->title }}
                            </p>
                            <p class="text-xs text-emerald-400/60 truncate mt-0.5">{{ $link->url }}</p>
                        </div>
                        <span class="shrink-0 text-[10px] font-medium px-2 py-0.5 rounded-md bg-slate-700/60 text-slate-500 group-hover:bg-slate-600/60 group-hover:text-slate-400 transition-colors">
                            {{ $link->link_type }}
                        </span>
                        <svg class="w-4 h-4 text-slate-600 group-hover:text-slate-400 transition-colors shrink-0 -ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                        </svg>
                    </a>
                @endforeach
            </div>
        </div>
    @endif
</div>
