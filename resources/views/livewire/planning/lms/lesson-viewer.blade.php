<div class="max-w-5xl mx-auto py-8 px-4 space-y-6">

    {{-- Header --}}
    <div>
        <a href="{{ route('planning.lms.monitor') }}"
           class="text-xs text-emerald-400 hover:text-emerald-300 mb-2 inline-flex items-center gap-1">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            Volver al monitor
        </a>
        <h1 class="text-xl font-bold text-white">Vista de Lección</h1>
        <p class="text-sm text-slate-400 mt-1">
            {{ $activity->topic ?? 'Actividad sin título' }}
            · {{ $activity->pevaluacion?->pensum?->asignatura?->name ?? '' }}
            · {{ $activity->pevaluacion?->pensum?->grado?->name ?? '' }} {{ $activity->pevaluacion?->seccion?->name ?? '' }}
        </p>
    </div>

    {{-- Status bar --}}
    @if($publication)
        <div class="flex items-center gap-3 p-3 rounded-lg bg-slate-800/40 border border-slate-700/50">
            <span @class([
                'px-2 py-0.5 rounded text-xs font-medium',
                'bg-emerald-500/10 text-emerald-400' => $publication->status === 'PUBLISHED',
                'bg-amber-500/10 text-amber-400'     => $publication->status === 'SCHEDULED',
                'bg-slate-500/10 text-slate-400'     => $publication->status === 'DRAFT',
                'bg-red-500/10 text-red-400'         => $publication->status === 'ARCHIVED',
            ])>
                {{ match($publication->status) {
                    'PUBLISHED' => 'Publicado',
                    'SCHEDULED' => 'Programado',
                    'ARCHIVED'  => 'Archivado',
                    default     => 'Borrador',
                } }}
            </span>
            <span class="text-xs text-slate-500">
                @if($publication->published_at)
                    Publicado: {{ $publication->published_at->format('d/m/Y H:i') }}
                @elseif($publication->publish_at)
                    Programado: {{ \Carbon\Carbon::parse($publication->publish_at)->format('d/m/Y H:i') }}
                @else
                    Sin publicar
                @endif
            </span>
            <span class="text-xs text-slate-600">·</span>
            <span class="text-xs text-slate-500">por {{ $publication->publisher?->name ?? '—' }}</span>
        </div>
    @endif

    {{-- Secciones del contenido --}}
    @forelse($sections as $section)
        <div class="bg-white rounded-xl shadow-lg overflow-hidden">
            {{-- Encabezado de la sección --}}
            <div class="px-6 py-4 bg-gradient-to-r from-emerald-600 to-emerald-700">
                <h2 class="text-lg font-bold text-white">{{ $section->title }}</h2>
            </div>
            {{-- Contenidos --}}
            <div class="px-6 py-4 space-y-4 divide-y divide-slate-100">
                @forelse($section->visibleContents as $content)
                    <div class="pt-4 first:pt-0">
                        @if($content->title)
                            <div class="flex items-start gap-2 mb-2">
                                <span class="w-0.5 h-5 bg-emerald-500 rounded-full mt-1 shrink-0"></span>
                                <h3 class="text-sm font-bold text-slate-800 leading-snug">{{ $content->title }}</h3>
                            </div>
                        @endif
                        <x-lms-content-renderer :body="$content->body ?? ''" :sanitize="false" />
                    </div>
                @empty
                    <p class="text-sm text-slate-400 italic py-4">Sin contenido en esta sección.</p>
                @endforelse
            </div>
        </div>
    @empty
        <div class="text-center py-16 bg-slate-800/30 rounded-xl border border-dashed border-slate-700">
            <svg class="w-16 h-16 text-slate-700 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
            </svg>
            <p class="text-sm font-medium text-slate-500">Esta lección no tiene secciones visibles.</p>
            <p class="text-xs text-slate-600 mt-1">El profesor debe agregar contenido antes de publicar.</p>
        </div>
    @endforelse

    {{-- Recursos --}}
    @if($resources->isNotEmpty())
        <div class="bg-slate-800/40 border border-slate-700/50 rounded-xl p-4">
            <h3 class="text-sm font-bold text-slate-300 uppercase tracking-wider mb-3 flex items-center gap-2">
                <svg class="w-4 h-4 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                </svg>
                Recursos descargables
            </h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                @foreach($resources as $res)
                    <div class="flex items-center gap-3 p-3 bg-slate-800/60 rounded-lg border border-slate-700/50">
                        <div class="w-8 h-8 rounded-lg bg-blue-500/10 flex items-center justify-center">
                            <svg class="w-4 h-4 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                            </svg>
                        </div>
                        <div class="min-w-0 flex-1">
                            <p class="text-sm font-medium text-slate-200 truncate">{{ $res->display_name }}</p>
                            @if($res->media)
                                <p class="text-xs text-slate-500">{{ $res->media->size_for_humans ?? '' }}</p>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    {{-- Enlaces --}}
    @if($links->isNotEmpty())
        <div class="bg-slate-800/40 border border-slate-700/50 rounded-xl p-4">
            <h3 class="text-sm font-bold text-slate-300 uppercase tracking-wider mb-3 flex items-center gap-2">
                <svg class="w-4 h-4 text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                </svg>
                Enlaces de interés
            </h3>
            <div class="space-y-2">
                @foreach($links as $link)
                    <div class="flex items-center gap-3 p-3 bg-slate-800/60 rounded-lg border border-slate-700/50">
                        <div class="w-8 h-8 rounded-lg bg-cyan-500/10 flex items-center justify-center">
                            <svg class="w-4 h-4 text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                            </svg>
                        </div>
                        <div class="min-w-0 flex-1">
                            <p class="text-sm font-medium text-slate-200 truncate">{{ $link->title }}</p>
                            <p class="text-xs text-cyan-400/70 truncate">{{ $link->url }}</p>
                        </div>
                        <span class="shrink-0 text-[10px] font-medium px-2 py-0.5 rounded bg-slate-700/60 text-slate-400">{{ $link->link_type }}</span>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</div>
