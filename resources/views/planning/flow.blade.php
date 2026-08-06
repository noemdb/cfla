@extends('planning.layouts.app')

@section('title', 'Planificación - Diagramas de Flujo')

@section('navbar-info')
    <div class="hidden lg:flex items-center gap-3 ml-4">
        <div class="flex items-center gap-1.5 text-xs text-gray-400">
            <svg class="w-3.5 h-3.5 text-cyan-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
            </svg>
            <span>{{ count($diagrams) === 1 ? '1 diagrama disponible' : count($diagrams).' diagramas disponibles' }}</span>
        </div>
    </div>
@endsection

@section('content')
    <div class="fade-in"
         x-data="{
            search: '',
            filterCategory: '',
            previewUrl: null,
            previewTitle: '',
            sortBy: 'order',
            get categories() {
                const cats = [...new Set(@js(collect($diagrams)->pluck('category')->filter()))];
                return cats;
            },
            get allDiagrams() {
                const base = @js($diagrams);
                if (this.sortBy === 'recent') {
                    return [...base].sort((a, b) => (b.updated_at || '').localeCompare(a.updated_at || ''));
                }
                if (this.sortBy === 'category') {
                    return [...base].sort((a, b) => (a.category || '').localeCompare(b.category || '') || (a.title || '').localeCompare(b.title || ''));
                }
                return [...base].sort((a, b) => (a.order || 999) - (b.order || 999));
            },
            get filteredDiagrams() {
                const q = this.search.toLowerCase().trim();
                return this.allDiagrams.filter(d => {
                    const haystack = [d.title, d.description, (d.badge || ''), (d.category || ''), (d.tags || []).join(' ')].join(' ').toLowerCase();
                    const matchQ = !q || haystack.includes(q);
                    const matchC = !this.filterCategory || d.category === this.filterCategory;
                    return matchQ && matchC;
                });
            },
            openPreview(url, title) {
                this.previewUrl = url;
                this.previewTitle = title;
            },
            closePreview() {
                this.previewUrl = null;
                this.previewTitle = '';
            }
         }">
        {{-- Encabezado --}}
        <div class="mb-8">
            <nav class="flex items-center gap-2 text-xs text-gray-500 mb-3" aria-label="Breadcrumb">
                <a href="{{ route('app.planning.index') }}" class="hover:text-emerald-300 transition-colors inline-flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                    Planificación
                </a>
                <svg class="w-3.5 h-3.5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                <span class="text-gray-300">Diagramas de Flujo</span>
            </nav>

            <h1 class="text-lg font-extrabold text-white mb-2">Diagramas de Flujo</h1>
            <p class="text-emerald-400 font-medium">Recursos visuales que explican los procesos académicos de la institución.</p>
        </div>

        {{-- Búsqueda y filtros --}}
        <div class="mb-6 flex flex-col sm:flex-row gap-3">
            <div class="relative flex-1">
                <svg class="w-4 h-4 text-gray-500 absolute left-3 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                <input type="search" x-model="search" placeholder="Buscar diagrama por título, descripción o etiqueta…"
                    class="w-full pl-10 pr-4 py-2.5 rounded-lg bg-gray-900/60 border border-white/10 text-sm text-gray-200 placeholder-gray-500 focus:border-emerald-500/50 focus:ring-2 focus:ring-emerald-500/20 outline-none transition-all">
            </div>
            <select x-model="filterCategory" class="px-4 py-2.5 rounded-lg bg-gray-900/60 border border-white/10 text-sm text-gray-300 focus:border-emerald-500/50 focus:ring-2 focus:ring-emerald-500/20 outline-none transition-all">
                <option value="">Todas las categorías</option>
                <template x-for="cat in categories" :key="cat">
                    <option :value="cat" x-text="cat"></option>
                </template>
            </select>
            <select x-model="sortBy" class="px-4 py-2.5 rounded-lg bg-gray-900/60 border border-white/10 text-sm text-gray-300 focus:border-emerald-500/50 focus:ring-2 focus:ring-emerald-500/20 outline-none transition-all">
                <option value="order">Orden: por relevancia</option>
                <option value="recent">Orden: más recientes</option>
                <option value="category">Orden: por categoría</option>
            </select>
        </div>

        {{-- Grilla de diagramas --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse ($diagrams as $diagram)
                @php($diagramUrl = route('app.planning.diagram.flow.show', $diagram['slug']))
                @php($accent = $diagram['accent'] ?? 'cyan')
                @php($accentText = $accent === 'cyan' ? 'text-cyan-400' : 'text-emerald-400')
                @php($accentBg = $accent === 'cyan' ? 'bg-cyan-500/10' : 'bg-emerald-500/10')
                @php($accentBorder = $accent === 'cyan' ? 'border-cyan-500/20' : 'border-emerald-500/20')
                @php($accentTop = $accent === 'cyan' ? 'border-t-cyan-500' : 'border-t-emerald-500')
                @php($accentIcon = $accent === 'cyan' ? 'text-cyan-400' : 'text-emerald-400')
                @php($accentHover = $accent === 'cyan' ? 'hover:bg-cyan-500/20' : 'hover:bg-emerald-500/20')
                @php($diagramTags = $diagram['tags'] ?? [])
                @php($hoverBorder = $accent === 'cyan' ? 'hover:border-cyan-500/30' : 'hover:border-emerald-500/30')
                <div
                    x-show="filteredDiagrams.some(d => d.slug === '{{ $diagram['slug'] }}')"
                    class="diagnostic-card group relative bg-gray-900/40 backdrop-blur-md border border-white/5 rounded-lg overflow-hidden transition-all duration-300 border-t-4 {{ $accentTop }} {{ $hoverBorder }}">
                    {{-- Enlace absoluto (toda la tarjeta es clicable) --}}
                    <a href="{{ $diagramUrl }}" target="_blank" rel="noopener" title="{{ $diagram['title'] ?? 'Abrir diagrama' }}"
                        class="absolute inset-0 z-0" aria-label="{{ $diagram['label'] ?? 'Abrir ' . ($diagram['title'] ?? 'diagrama') }} en una pestaña nueva"></a>

                    {{-- Thumbnail / vista previa visual --}}
                    <div class="relative h-36 overflow-hidden border-b border-white/5 {{ $accentBg }} pointer-events-none">
                        <div class="absolute -top-8 -right-6 opacity-10 group-hover:opacity-20 transition-opacity">
                            <svg class="w-32 h-32 {{ $accentIcon }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path></svg>
                        </div>

                        {{-- Mini-diagrama conceptual del flujo --}}
                        <div class="relative z-10 h-full w-full flex items-center justify-center gap-2 px-6">
                            @php($nodeClasses = "w-10 h-10 md:w-12 md:h-12 rounded-lg border flex items-center justify-center {{ $accentBg }} {{ $accentBorder }}")
                            @php($lineClasses = "flex-1 h-px max-w-8 {{ $accent === 'cyan' ? 'bg-cyan-500/40' : 'bg-emerald-500/40' }}")

                            <div class="{{ $nodeClasses }}">
                                <svg class="w-5 h-5 {{ $accentIcon }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"></path></svg>
                            </div>
                            <div class="{{ $lineClasses }}"></div>
                            <div class="{{ $nodeClasses }}">
                                <svg class="w-5 h-5 {{ $accentIcon }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h8m-8-4h8M5 3a2 2 0 110 4 2 2 0 010-4zm0 14a2 2 0 110 4 2 2 0 010-4zm14-7a2 2 0 110 4 2 2 0 010-4z"></path></svg>
                            </div>
                            <div class="{{ $lineClasses }}"></div>
                            <div class="{{ $nodeClasses }}">
                                <svg class="w-5 h-5 {{ $accentIcon }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                            </div>
                        </div>
                    </div>

                    <div class="relative z-10 flex flex-col h-full p-6 pointer-events-none">
                        <div class="w-12 h-12 {{ $accentBg }} rounded-lg flex items-center justify-center mb-2 group-hover:scale-110 transition-transform duration-300">
                            <svg class="w-6 h-6 {{ $accentIcon }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path></svg>
                        </div>
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[9px] font-bold uppercase tracking-wider {{ $accentBg }} {{ $accentText }} {{ $accentBorder }} mb-3">{{ $diagram['badge'] }}</span>
                        <h3 class="text-lg font-bold text-white mb-2">{{ $diagram['title'] }}</h3>
                        <p class="text-gray-400 text-sm leading-relaxed mb-6">{{ $diagram['description'] }}</p>

                        {{-- Metadatos adicionales --}}
                        <div class="grid grid-cols-2 gap-2 mb-4 text-[10px]">
                            @if (($diagram['category'] ?? null))
                                <div class="flex items-center gap-1.5 text-gray-400">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 3h10a1 1 0 011 1v16a1 1 0 01-1 1H7a1 1 0 01-1-1V4a1 1 0 011-1z"></path></svg>
                                    <span>{{ $diagram['category'] }}</span>
                                </div>
                            @endif
                            @if (($diagram['updated_at'] ?? null))
                                <div class="flex items-center gap-1.5 text-gray-400">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                    <span>{{ $diagram['updated_at'] }}</span>
                                </div>
                            @endif
                            @if (($diagram['duration'] ?? null))
                                <div class="flex items-center gap-1.5 text-gray-400">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    <span>{{ $diagram['duration'] }}</span>
                                </div>
                            @endif
                            @if (($diagram['audience'] ?? null))
                                <div class="flex items-center gap-1.5 text-gray-400">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a4 4 0 00-3-3.87M9 20H4v-2a4 4 0 013-3.87m6-1.13a4 4 0 10-4-4 4 4 0 004 4zm6-4a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                    <span>{{ $diagram['audience'] }}</span>
                                </div>
                            @endif
                        </div>

                        @if (count($diagramTags))
                            <div class="mt-auto flex flex-wrap gap-1.5 mb-4">
                                @foreach ($diagramTags as $tag)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[9px] font-semibold {{ $accentBg }} {{ $accentText }}">{{ $tag }}</span>
                                @endforeach
                            </div>
                        @endif

                        <div class="flex items-center justify-between gap-2 flex-wrap pointer-events-auto">
                            {{-- Badge de estado --}}
                            @if (($diagram['status'] ?? null))
                                @php($status = $diagram['status'])
                                @php($statusClass = match ($status) {
                                    'nuevo' => 'bg-emerald-500/15 text-emerald-300 border-emerald-500/30',
                                    'actualizado' => 'bg-cyan-500/15 text-cyan-300 border-cyan-500/30',
                                    'desactualizado' => 'bg-red-500/15 text-red-300 border-red-500/30',
                                    default => 'bg-gray-500/15 text-gray-300 border-gray-500/30',
                                })
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[9px] font-bold uppercase tracking-wider border {{ $statusClass }}">
                                    {{ $status }}
                                </span>
                            @endif

                            <div class="flex items-center gap-2">
                                <button type="button"
                                    @click="openPreview('{{ $diagramUrl }}', '{{ $diagram['title'] }}')"
                                    class="inline-flex items-center gap-2 px-3 py-2 bg-white/5 hover:bg-white/10 text-gray-300 rounded-lg border border-white/10 transition-all duration-300 text-xs font-bold uppercase tracking-widest">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.964 7.183.036.21.036.428 0 .639C20.577 16.49 16.638 19.5 12 19.5c-4.638 0-8.573-3.007-9.964-7.183z"/></svg>
                                    Vista previa
                                </button>
                            <a href="{{ $diagramUrl }}" target="_blank" rel="noopener"
                                title="{{ $diagram['title'] ?? 'Abrir diagrama' }}"
                                class="inline-flex items-center gap-2 px-4 py-2 {{ $accentBg }} {{ $accentHover }} {{ $accentText }} rounded-lg border {{ $accentBorder }} transition-all duration-300 text-xs font-bold uppercase tracking-widest group/btn">
                                <svg class="w-4 h-4 transition-transform group-hover/btn:rotate-12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37a1.724 1.724 0 002.572-1.065z"/></svg>
                                Ver Diagrama
                            </a>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-full rounded-lg border border-dashed border-white/10 bg-gray-900/40 p-10 text-center">
                    <p class="text-gray-500 text-sm">Aún no hay diagramas de flujo publicados.</p>
                </div>
            @endforelse
        </div>

        {{-- Mensaje cuando la búsqueda no encuentra resultados --}}
        <div x-show="filteredDiagrams.length === 0" x-cloak class="mt-6 rounded-lg border border-dashed border-white/10 bg-gray-900/40 p-10 text-center">
            <p class="text-gray-500 text-sm">No se encontraron diagramas con los filtros actuales.</p>
            <button type="button" @click="search=''; filterCategory=''" class="mt-3 inline-flex items-center gap-2 px-4 py-2 bg-emerald-500/10 hover:bg-emerald-500/20 text-emerald-400 rounded-lg border border-emerald-500/20 text-xs font-bold uppercase tracking-widest transition-all">
                Limpiar filtros
            </button>
        </div>

        {{-- Nota sobre el patrón de URLs --}}
        <div class="mt-10 rounded-lg border border-white/5 bg-gray-900/40 p-5">
            <h4 class="text-xs font-bold uppercase tracking-widest text-gray-500 mb-2 flex items-center gap-2">
                <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                ¿Cómo se publican estos recursos?
            </h4>
            <p class="text-sm text-gray-400 leading-relaxed mb-3">
                Cada infografía vive como un archivo estático en <code class="text-cyan-400 bg-cyan-500/10 px-1 py-0.5 rounded text-xs break-all inline-block max-w-full align-bottom">docs/infografia/flujo{{ '{' }}Nombre{{ '}' }}.html</code> y queda disponible automáticamente bajo la URL:
            </p>
            <div class="flex items-center gap-2 flex-wrap">
                <code class="text-xs bg-black/40 border border-white/10 text-emerald-300 px-3 py-1.5 rounded-lg font-mono break-all">{{ url('app/planning/diagram/flow') }}/*</code>
                <span class="text-xs text-gray-500">→</span>
                <code class="text-xs bg-black/40 border border-white/10 text-gray-300 px-3 py-1.5 rounded-lg font-mono break-all">{{ route('app.planning.diagram.flow.show', 'activity-lesson') }}</code>
            </div>
        </div>

        {{-- Modal vista previa --}}
        <div x-show="previewUrl" x-cloak
            class="fixed inset-0 z-[9999] flex items-center justify-center p-4 sm:p-8"
            role="dialog" aria-modal="true" aria-label="Vista previa del diagrama">
            <div class="absolute inset-0 bg-black/80 backdrop-blur-sm" @click="closePreview()"></div>
            <div class="relative w-full h-full max-w-6xl bg-slate-900 rounded-xl border border-emerald-500/20 flex flex-col overflow-hidden shadow-2xl">
                <div class="flex items-center justify-between gap-3 px-4 py-3 border-b border-white/10 bg-slate-900/90">
                    <div class="flex items-center gap-2 min-w-0">
                        <svg class="w-4 h-4 text-emerald-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path></svg>
                        <h3 class="text-sm font-bold text-white truncate" x-text="previewTitle"></h3>
                    </div>
                    <button type="button" @click="closePreview()"
                        class="shrink-0 inline-flex items-center gap-1.5 px-3 py-1.5 bg-white/5 hover:bg-white/10 rounded-lg text-xs text-gray-300 transition-all">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        Cerrar
                    </button>
                </div>
                <div class="flex-1 bg-white">
                    <iframe :src="previewUrl" class="w-full h-full" title="Vista previa del diagrama"></iframe>
                </div>
            </div>
        </div>
    </div>
@endsection
