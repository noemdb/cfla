<div class="w-full mx-auto py-2 px-4 space-y-6">

    @if($mode === 'list')
    @include("livewire.profesor.lms._list")

    @elseif($mode === 'wizard')
        {{-- ═══════════ ASISTENTE PASO A PASO ═══════════ --}}
        <div wire:key="mode-wizard">

        {{-- Overlay bloqueante con competencias e indicadores de la actividad --}}
        @php
            $act = $selectedActivity;
            $pe = $act?->pevaluacion;
            $pensum = $pe?->pensum;
            $competencias = $pensum?->diagCompetencies()->with(['indicators'])->get();
            $indicadoresLogro = $act?->achievements ?? collect();
            $gradoName = $pensum?->grado?->name ?? '—';
            $asignaturaName = $pensum?->asignatura?->name ?? '—';
            $seccionName = $pe?->seccion?->name ?? '—';
        @endphp

        <div wire:loading.flex
             wire:target="generateStep1Content,generateStep2Sections,generateSectionContent,generateSlideText,generateSlideImage,generateSlideDiagram,generateSectionIllustration,generateReviewQuestions,generateSlideHtmlTags,generateSlideMath"
             class="fixed inset-0 z-[9999] items-center justify-center bg-white/95 dark:bg-slate-900/90 backdrop-blur-md"
             id="llm-loading-overlay"
             x-data="{
                 startTime: Date.now(),
                 elapsed: '00:00',
                 __timer: null,
                 __obs: null,
                 init() {
                     this.__timer = setInterval(() => {
                         if (this.$el.style.display === 'none') return;
                         var diff = Math.floor((Date.now() - this.startTime) / 1000);
                         var m = String(Math.floor(diff / 60)).padStart(2, '0');
                         var s = String(diff % 60).padStart(2, '0');
                         this.elapsed = m + ':' + s;
                     }, 1000);
                     this.__obs = new MutationObserver(function () {
                         if (this.$el.style.display !== 'none' && this.$el.style.display !== '') {
                             this.startTime = Date.now();
                             this.elapsed = '00:00';
                         }
                     }.bind(this));
                     this.__obs.observe(this.$el, { attributes: true, attributeFilter: ['style'] });
                 },
                 destroy() {
                     if (this.__timer) clearInterval(this.__timer);
                     if (this.__obs) this.__obs.disconnect();
                 }
             }">
            <div class="max-w-4xl py-8 mx-auto px-6 space-y-5">

                {{-- Header --}}
                <div class="text-center space-y-3">
                    <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-gradient-to-br from-purple-500/20 to-emerald-500/20 border-2 border-purple-500/30 mx-auto relative">
                        <svg class="w-8 h-8 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.813 15.904L9 18.75l-.813-2.846a4.5 4.5 0 00-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 003.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 003.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 00-3.09 3.09z"/>
                        </svg>
                        {{-- Spinner ring around the icon --}}
                        <svg class="absolute inset-0 w-full h-full animate-spin text-purple-400/40" viewBox="0 0 64 64" fill="none">
                            <circle cx="32" cy="32" r="28" stroke="currentColor" stroke-width="3" stroke-dasharray="44 132" stroke-linecap="round" class="opacity-80"/>
                        </svg>
                    </div>
                    <p class="text-lg font-bold text-purple-200">Generando contenido con IA</p>
                    {{-- Contador de tiempo --}}
                    <p class="text-xs font-mono text-purple-300/70 flex items-center justify-center gap-1.5">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span x-text="elapsed"></span>
                    </p>
                    @if($act)
                        <p class="text-sm text-gray-500 dark:text-slate-400">{{ $asignaturaName }} · {{ $gradoName }} · Sec. {{ $seccionName }}</p>
                    @endif
                </div>

                <div class="space-y-4 text-left max-h-[60vh] overflow-y-auto"
                     x-data="{ openCompetencias: false, openIndicadores: false }">

                    {{-- Competencias (acordeón, cerrado por defecto) --}}
                    <div class="w-full bg-white dark:bg-slate-800/50 border border-gray-200 dark:border-slate-700/50 rounded-lg overflow-hidden">
                        {{-- Header clickeable --}}
                        <button @click="openCompetencias = !openCompetencias"
                                class="w-full flex items-center gap-3 px-5 py-2.5 bg-gray-100 dark:bg-slate-800/40 border-b border-gray-200 dark:border-slate-700/30 hover:bg-gray-200 dark:hover:bg-slate-800/60 transition-colors text-left">
                            <div class="w-9 h-9 rounded-lg bg-gradient-to-br from-purple-500/20 to-purple-600/10 flex items-center justify-center shrink-0">
                                <svg class="w-5 h-5 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.48 3.499a.562.562 0 011.04 0l2.125 5.111a.563.563 0 00.475.345l5.518.442c.499.04.701.663.321.988l-4.204 3.602a.563.563 0 00-.182.557l1.285 5.385a.562.562 0 01-.84.61l-4.725-2.885a.563.563 0 00-.586 0L6.982 20.54a.562.562 0 01-.84-.61l1.285-5.386a.562.562 0 00-.182-.557l-4.204-3.602a.563.563 0 01.321-.988l5.518-.442a.563.563 0 00.475-.345L11.48 3.5z"/>
                                </svg>
                            </div>
                            <div class="flex-1 min-w-0">
                                <h3 class="text-sm font-bold text-purple-200">Competencias</h3>
                                <p class="text-[11px] text-gray-400 dark:text-slate-500 truncate">Competencias fundamentales del pensum</p>
                            </div>
                            <div class="flex items-center gap-2 shrink-0">
                                @if($competencias?->isNotEmpty())
                                    <span class="text-xs font-medium text-purple-300 bg-purple-500/10 border border-purple-500/20 px-2.5 py-0.5 rounded-full">
                                        {{ $competencias->count() }}
                                    </span>
                                @endif
                                <svg class="w-4 h-4 text-gray-400 dark:text-slate-500 transition-transform duration-200"
                                     :class="openCompetencias ? 'rotate-180' : ''"
                                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                </svg>
                            </div>
                        </button>

                        {{-- Body colapsable --}}
                        <div x-show="openCompetencias"
                             x-cloak
                             x-transition:enter.duration.150ms>
                            @if($competencias?->isNotEmpty())
                                <div class="p-4 grid grid-cols-1 md:grid-cols-2 gap-3">
                                    @foreach($competencias as $comp)
                                        @php $indCount = $comp->indicators?->count() ?? 0; @endphp
                                        <div class="bg-white dark:bg-slate-800/70 border border-purple-500/20 rounded-lg overflow-hidden">
                                            <div class="h-1 bg-gradient-to-r from-purple-500/60 to-purple-400/30 shrink-0"></div>
                                            <div class="p-4 flex flex-col gap-2">
                                                <p class="text-sm font-semibold text-gray-900 dark:text-white leading-snug">{{ $comp->name }}</p>
                                                @if($indCount > 0)
                                                    <div class="space-y-1">
                                                        @foreach($comp->indicators as $ind)
                                                            <div class="flex items-start gap-1.5">
                                                                <span class="w-1 h-1 rounded-full bg-purple-400/40 mt-1.5 shrink-0"></span>
                                                                <p class="text-xs text-gray-500 dark:text-slate-400 leading-relaxed">{{ $ind->description }}</p>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="flex flex-col items-center justify-center py-8 px-4 text-center">
                                    <div class="w-12 h-12 rounded-full bg-gray-200 dark:bg-slate-700/30 flex items-center justify-center mb-2">
                                        <svg class="w-6 h-6 text-gray-400 dark:text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M11.48 3.499a.562.562 0 011.04 0l2.125 5.111a.563.563 0 00.475.345l5.518.442c.499.04.701.663.321.988l-4.204 3.602a.563.563 0 00-.182.557l1.285 5.385a.562.562 0 01-.84.61l-4.725-2.885a.563.563 0 00-.586 0L6.982 20.54a.562.562 0 01-.84-.61l1.285-5.386a.562.562 0 00-.182-.557l-4.204-3.602a.563.563 0 01.321-.988l5.518-.442a.563.563 0 00.475-.345L11.48 3.5z"/>
                                        </svg>
                                    </div>
                                    <p class="text-sm text-gray-400 dark:text-slate-500 italic">No hay competencias asociadas</p>
                                </div>
                            @endif
                        </div>
                    </div>

                    {{-- Actividad de referencia --}}
                    @if($act)
                        <div class="bg-gray-50 dark:bg-slate-800/30 border border-gray-200 dark:border-slate-700/30 rounded-lg p-4">
                            <div class="flex items-center gap-2 mb-2">
                                <svg class="w-4 h-4 text-gray-400 dark:text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                                <span class="text-xs font-bold text-gray-400 dark:text-slate-500 uppercase tracking-wider">Actividad</span>
                            </div>
                            <p class="text-sm text-gray-500 dark:text-slate-400">{{ $act->topic }}</p>
                        </div>
                    @endif
                </div>

                {{-- Bouncing dots --}}
                <div class="flex items-center justify-center gap-1.5 pt-1">
                    <span class="w-2.5 h-2.5 rounded-full bg-purple-400 animate-bounce" style="animation-delay:0s"></span>
                    <span class="w-2.5 h-2.5 rounded-full bg-purple-400 animate-bounce" style="animation-delay:0.15s"></span>
                    <span class="w-2.5 h-2.5 rounded-full bg-purple-400 animate-bounce" style="animation-delay:0.3s"></span>
                </div>
            </div>
        </div>

        {{-- Overlay de resultado (sin animación, se muestra inmediatamente al completar) --}}
        @if($showGenerationResult)
            <div class="fixed inset-0 z-[9999] flex items-center justify-center bg-white/95 dark:bg-slate-900/90 backdrop-blur-md">
                <div class="text-center space-y-5 max-w-2xl py-8 mx-auto px-6">
                    <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-emerald-500/20 border-2 border-emerald-500/40 mx-auto">
                        <svg class="w-8 h-8 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                    </div>

                    <p class="text-lg font-bold text-emerald-300">
                        @if($generationType === 'step1')
                            Título y descripción generados
                        @elseif($generationType === 'step2')
                            Secciones generadas
                        @else
                            Contenido generado
                        @endif
                    </p>

                    @if($generationType === 'step1')
                        <div class="bg-white dark:bg-slate-800/50 border border-gray-200 dark:border-slate-700/50 rounded-lg p-6 text-left space-y-3 min-h-[80px]">
                            <h2 class="text-base font-bold text-gray-900 dark:text-white">{{ $lessonTitle }}</h2>
                            <p class="text-sm text-gray-600 dark:text-slate-300 leading-relaxed">{{ $lessonDescription }}</p>
                        </div>
                    @elseif($generationType === 'section')
                        <div class="bg-white dark:bg-slate-800/50 border border-gray-200 dark:border-slate-700/50 rounded-lg p-6 text-left">
                            <p class="text-sm text-gray-600 dark:text-slate-300 leading-relaxed">
                                {{ \Illuminate\Support\Str::limit(
                                    ($wizardSections[array_key_last($wizardSections)]['contents'][0]['body'] ?? ''),
                                    300
                                ) }}
                            </p>
                        </div>
                    @elseif($generationType === 'step2')
                        <div class="bg-white dark:bg-slate-800/50 border border-gray-200 dark:border-slate-700/50 rounded-lg p-6 text-left max-h-80 overflow-y-auto space-y-3">
                            @foreach($wizardSections as $section)
                                <div class="flex items-start gap-2">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 mt-1.5 shrink-0"></span>
                                    <div>
                                        <p class="text-sm font-bold text-emerald-300">{{ $section['title'] ?? '' }}</p>
                                        <p class="text-xs text-gray-500 dark:text-slate-400 leading-relaxed mt-0.5">
                                            {{ \Illuminate\Support\Str::limit($section['contents'][0]['body'] ?? '', 150) }}
                                        </p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif

                    <div class="flex items-center justify-center mt-2">
                        <button wire:click="dismissGenerationResult"
                                class="px-6 py-2 bg-emerald-600 hover:bg-emerald-500 text-white text-sm font-medium rounded-lg transition-all">
                            Continuar
                        </button>
                    </div>
                </div>
            </div>
        @endif

        {{-- Encabezado del wizard --}}
        <div class="flex flex-col gap-1">
            <div class="flex items-center gap-3 min-w-0 shrink">
                <button wire:click="backToList"
                        class="p-2 text-gray-500 dark:text-slate-400 hover:text-gray-900 dark:hover:text-white hover:bg-gray-100 dark:hover:bg-slate-700/50 rounded-lg transition-all">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                </button>
                <div>
                    <h1 class="text-lg font-bold text-gray-900 dark:text-white">{{ $lessonTitle ?: 'Nueva Lección' }}</h1>
                    <p class="text-xs text-gray-500 dark:text-slate-400">{{ $selectedActivity?->pevaluacion?->pensum?->asignatura?->name ?? '—' }} · {{ $selectedActivity?->pevaluacion?->pensum?->grado?->name ?? '—' }} Sec.{{ $selectedActivity?->pevaluacion?->seccion?->name ?? '—' }}</p>
                </div>

                {{-- Estado de publicación --}}
                @php $hasSchedule = !blank($this->publishAt); @endphp
                @if($isPublished)
                    <div class="flex items-center gap-1.5 px-2.5 py-1.5 rounded-lg bg-emerald-500/8 border border-emerald-500/15 shrink-0 ml-auto" role="alert">
                        <svg class="w-3.5 h-3.5 text-emerald-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span class="text-[10px] font-medium text-emerald-400 whitespace-nowrap">🟢 Publicado</span>
                    </div>
                @elseif($hasSchedule)
                    <div class="flex items-center gap-1.5 px-2.5 py-1.5 rounded-lg bg-amber-500/8 border border-amber-500/15 shrink-0 ml-auto" role="alert">
                        <svg class="w-3.5 h-3.5 text-amber-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span class="text-[10px] font-medium text-amber-400 whitespace-nowrap">⏰ {{ \Carbon\Carbon::parse($this->publishAt)->format('d/m/Y H:i') }}</span>
                    </div>
                @else
                    <div class="flex items-center gap-1.5 px-2.5 py-1.5 rounded-lg bg-slate-700/30 border border-slate-600/30 shrink-0 ml-auto" role="alert">
                        <svg class="w-3.5 h-3.5 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                        <span class="text-[10px] font-medium text-slate-400 whitespace-nowrap">📋 Borrador</span>
                    </div>
                @endif
            </div>

        </div>

        {{-- Banner de solo lectura para lecciones publicadas --}}
        @if($isPublished)
            <div class="bg-amber-500/12 border border-amber-500/20 rounded-lg p-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-amber-500/15 flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-amber-300">Lección publicada — solo lectura</h3>
                        <p class="text-xs text-gray-500 dark:text-slate-400">Esta lección ya está publicada y no puede ser modificada. Puedes visualizar todo su contenido y usar la vista previa para estudiantes.</p>
                    </div>
                </div>
            </div>
        @endif

        {{-- Mensaje de publicación exitosa --}}
        @if($published)
            <div class="bg-emerald-500/10 border border-emerald-500/20 rounded-lg p-4 text-center">
                <svg class="w-12 h-12 text-emerald-400 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <h3 class="text-base font-bold text-emerald-400 mb-1">¡Lección publicada exitosamente!</h3>
                <p class="text-sm text-gray-500 dark:text-slate-400 mb-2">El contenido ya está disponible para los estudiantes.</p>
                <div class="flex items-center justify-center gap-3">
                    {{-- <button wire:click="openListStudentPreview({{ $selectedActivityId }})"
                            class="px-4 py-2 bg-fuchsia-600 hover:bg-fuchsia-500 text-white text-sm rounded-lg font-medium transition-all">
                        👁️ Vista estudiante
                    </button> --}}
                    <button wire:click="goToStep(1)"
                            class="px-4 py-2 bg-emerald-600 hover:bg-emerald-500 text-white text-sm rounded-lg font-medium transition-all">
                        Editar contenido completo
                    </button>
                    <button wire:click="backToList"
                            class="px-4 py-2 bg-gray-100 dark:bg-slate-700 hover:bg-gray-200 dark:hover:bg-slate-600 text-gray-700 dark:text-slate-300 text-sm rounded-lg font-medium transition-all">
                        Volver al listado
                    </button>
                </div>
            </div>
        @else
            <div x-on:keydown.window="
                if (event.ctrlKey || event.metaKey) {
                    if (event.key === 'ArrowRight') {
                        event.preventDefault();
                        $wire.call('goToStep', Math.min(5, {{ $currentStep }} + 1));
                    } else if (event.key === 'ArrowLeft') {
                        event.preventDefault();
                        $wire.call('goToStep', Math.max(1, {{ $currentStep }} - 1));
                    }
                }
                if ({{ $currentStep === 2 ? 'true' : 'false' }}) {
                    if (event.key === 'ArrowDown' && !event.ctrlKey && !event.metaKey) {
                        event.preventDefault();
                        $wire.call('nextSlide');
                    } else if (event.key === 'ArrowUp' && !event.ctrlKey && !event.metaKey) {
                        event.preventDefault();
                        $wire.call('prevSlide');
                    }
                }
            ">

            {{-- Navegación entre pasos --}}
            @php
                $topBorderStyle = match(true) {
                    $isPublished     => 'border-top: 4px solid rgba(59,130,246,0.35)',   // blue-500/35 (azul rey)
                    !blank($this->publishAt) => 'border-top: 4px solid rgba(168,85,247,0.35)',  // purple-500/35 (púrpura)
                    default          => 'border-top: 4px solid rgba(251,191,36,0.35)',  // amber-400/35 (warning)
                };
            @endphp
            <div class="flex items-start justify-between my-4 border-x border-b border-gray-200 dark:border-slate-700 rounded-lg px-4 py-5" style="min-height: 5rem; {{ $topBorderStyle }}">
                <button wire:click="goToStep({{ $currentStep - 1 }})"
                        class="px-4 py-2 text-sm text-gray-500 dark:text-slate-400 hover:text-gray-900 dark:hover:text-white transition-colors {{ $currentStep <= 1 ? 'invisible' : '' }}">
                    ← Anterior
                </button>

                {{-- Barra de progreso --}}
                <div class="flex-1 flex justify-center min-w-0 px-2 mx-2 py-2">
                    <div class="flex items-center gap-0.5 sm:gap-1 min-w-max" style="overflow: visible;">
                        @php $stepLabels = ['', 'Información', 'Secciones', 'Recursos', 'Repaso', 'Publicar']; @endphp
                        @foreach(range(1, 5) as $step)
                            <button wire:click="goToStep({{ $step }})" type="button" class="flex items-center gap-1 group shrink-0">
                                <div class="flex flex-col items-center min-w-0">
                                    <span class="inline-flex items-center justify-center rounded-full text-[11px] font-bold transition-all duration-200
                                        {{ $currentStep === $step ? 'w-7 h-7 bg-emerald-500 text-white shadow-md shadow-emerald-500/40 ring-2 ring-emerald-400/40 scale-110' : ($currentStep > $step ? 'w-6 h-6 bg-emerald-500/20 text-emerald-400' : 'w-6 h-6 bg-gray-200 dark:bg-slate-700 text-gray-500 dark:text-slate-500') }}
                                        hover:ring-2 hover:ring-emerald-400/30 hover:ring-offset-1 hover:ring-offset-white dark:hover:ring-offset-slate-900">
                                        @if($currentStep > $step)
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                        @else
                                            {{ $step }}
                                        @endif
                                    </span>
                                    <span class="text-[9px] mt-0.5 whitespace-nowrap {{ $currentStep === $step ? 'text-emerald-400 font-semibold' : 'text-gray-400 dark:text-slate-500' }}
                                        {{ $step === $currentStep ? '' : 'hidden sm:inline' }}">
                                        {{ $stepLabels[$step] }}
                                    </span>
                                </div>
                                @if($step < 5)
                                    <span class="w-4 sm:w-8 h-px {{ $currentStep > $step ? 'bg-emerald-500/40' : 'bg-gray-200 dark:bg-slate-700' }}"></span>
                                @endif
                            </button>
                        @endforeach
                        @php
                            $completedSteps = collect(range(1,5))->filter(fn($s) => $s < $currentStep)->count();
                        @endphp
                        <span class="ml-3 text-[10px] font-mono text-gray-400 dark:text-slate-500 shrink-0 whitespace-nowrap">{{ $completedSteps }}/5</span>
                    </div>
                </div>

                <div class="flex items-center gap-2">
                    @if($currentStep < 5)
                        <button wire:click="goToStep({{ $currentStep + 1 }})"
                                class="px-5 py-2 bg-emerald-600 hover:bg-emerald-500 text-white text-sm font-medium rounded-lg transition-all">
                            Siguiente →
                        </button>
                    @endif
                </div>
            </div>

            {{-- Grid: formulario a la izquierda, preview a la derecha --}}
            <div class="grid grid-cols-1 lg:grid-cols-5 gap-6">

                {{-- ═══ COLUMNA IZQUIERDA: FORMULARIO (3/5) ═══ --}}
                <div class="lg:col-span-5 space-y-4 transition-all duration-300 min-w-0">

                    {{-- STEP 1: Información de la Lección --}}
                    @if($currentStep === 1)
                        @include("livewire.profesor.lms._wizard-step-1")
                    @endif

                    {{-- STEP 2: Editor de Diapositivas (Slide Editor) --}}
                    @if($currentStep === 2)
                        @include("livewire.profesor.lms._wizard-step-2")
                    @endif

                                        {{-- STEP 3: Recursos y Enlaces — Tabbed interface --}}
                    @if($currentStep === 3)
                        @include("livewire.profesor.lms._wizard-step-3")
                    @endif

                    {{-- STEP 4: Preguntas de Repaso --}}
                    @if($currentStep === 4)
                        @include("livewire.profesor.lms._wizard-step-4")
                    @endif

                    {{-- STEP 5: Publicar --}}
                    @if($currentStep === 5)
                        @include("livewire.profesor.lms._wizard-step-5")
                    @endif

                </div>

            </div>


            {{-- Botón flotante: abrir preview full-screen --}}
            {{-- <button wire:click="$set('showFullPreview', true)"
                    class="hidden lg:flex items-center gap-1.5 text-xs text-slate-400 hover:text-emerald-400 transition-all bg-slate-800/90 border border-slate-700 rounded-l-xl pl-3 pr-2.5 py-2 fixed right-0 top-1/2 -translate-y-1/2 z-40 cursor-pointer"
                    title="Vista previa">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"/>
                </svg>
                <span>Previa</span>
            </button> --}}

            @include("livewire.profesor.lms._full-preview-modal")

            {{-- ═══════════ MODAL VISTA ESTUDIANTE (wizard → unificado) ═══════════ --}}
            {{-- openWizardStudentPreview() normaliza los datos del wizard y activa el componente unificado --}}

            {{-- ═══════════ MODAL CONFIRMACIÓN GUARDAR SIN SECCIONES ═══════════ --}}
            @if($showUnsavedConfirm)
                <div class="fixed inset-0 z-[9999] overflow-y-auto" wire:key="unsaved-confirm-modal">
                    <div class="fixed inset-0 bg-black/70 backdrop-blur-sm"></div>
                    <div class="relative min-h-screen flex items-center justify-center p-4">
                        <div class="relative w-full max-w-md bg-gray-900 border border-slate-700 rounded-lg shadow-2xl overflow-hidden">
                            <div class="p-6 space-y-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-full bg-amber-500/20 flex items-center justify-center shrink-0">
                                        <svg class="w-5 h-5 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M4.93 4.93a10 10 0 0114.14 0m-1.41 1.41a6 6 0 00-8.48 0m1.41 1.41a2 2 0 012.83 0"/>
                                        </svg>
                                    </div>
                                    <div>
                                        <h3 class="text-sm font-bold text-white uppercase tracking-wider">Sin diapositivas</h3>
                                        <p class="text-xs text-slate-400 mt-1">
                                            Esta lección no tiene diapositivas ni contenido. ¿Guardar de todas formas?
                                        </p>
                                    </div>
                                </div>

                                <div class="flex items-center justify-end gap-3 pt-2">
                                    <button wire:click="$set('showUnsavedConfirm', false)"
                                            class="px-4 py-2 text-sm text-slate-400 hover:text-white transition-colors">
                                        Cancelar
                                    </button>
                                    <button wire:click="confirmSaveAnyway"
                                            class="px-5 py-2 bg-amber-600 hover:bg-amber-500 text-white text-sm font-medium rounded-lg transition-all">
                                        {{ $pendingSaveAction === 'confirmPublish' ? 'Programar de todas formas' : 'Guardar de todas formas' }}
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            {{-- ═══════════ MODAL CONFIRMACIÓN PUBLICAR SIN FECHA ═══════════ --}}
            @if($showPublishConfirm)
                <div class="fixed inset-0 z-[9999] overflow-y-auto" wire:key="publish-confirm-modal">
                    <div class="fixed inset-0 bg-black/70 backdrop-blur-sm"></div>
                    <div class="relative min-h-screen flex items-center justify-center p-4">
                        <div class="relative w-full max-w-md bg-gray-900 border border-slate-700 rounded-lg shadow-2xl overflow-hidden">
                            <div class="p-6 space-y-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-full bg-amber-500/20 flex items-center justify-center shrink-0">
                                        <svg class="w-5 h-5 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4.5c-.77-.833-2.694-.833-3.464 0L3.34 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                                        </svg>
                                    </div>
                                    <div>
                                        <h3 class="text-sm font-bold text-white uppercase tracking-wider">Sin fecha de publicación</h3>
                                        <p class="text-xs text-slate-400 mt-1">
                                            No has establecido una fecha de publicación. La lección se guardará como borrador.
                                        </p>
                                    </div>
                                </div>

                                <div class="flex items-center justify-end gap-3 pt-2">
                                    <button wire:click="$set('showPublishConfirm', false)"
                                            class="px-4 py-2 text-sm text-slate-400 hover:text-white transition-colors">
                                        Cancelar
                                    </button>
                                    <button wire:click="saveAndPublish"
                                            class="px-5 py-2 bg-emerald-600 hover:bg-emerald-500 text-white text-sm font-medium rounded-lg transition-all"
                                            @disabled($isPublished)>
                                        Guardar de todas formas
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            {{-- ═══ BOTONES FLOTANTES: Ayuda + Vista estudiante + Guardar ═══ --}}
            <div class="fixed bottom-6 right-6 z-50 flex flex-col items-end gap-2">
                <div class="flex">
                <button wire:click="$set('showHelpModal', true)"
                        title="Ayuda del wizard"
                        class="inline-flex items-center justify-center w-11 h-11 rounded-l-xl text-sm font-semibold transition-all duration-200
                               text-slate-400 bg-slate-800/80 hover:bg-slate-700 hover:text-white
                               border border-slate-600/30 hover:border-slate-500/50
                               active:scale-[0.95]
                               border-r-0">
                    <svg class="w-[18px] h-[18px] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.879 7.519c1.171-1.025 3.071-1.025 4.242 0 1.172 1.025 1.172 2.687 0 3.712-.203.179-.43.326-.67.442-.745.361-1.45.999-1.45 1.827v.75M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9 5.25h.008v.008H12v-.008z"/>
                    </svg>
                </button>

                <button wire:click="openWizardStudentPreview"
                        title="Vista estudiante"
                        class="inline-flex items-center justify-center w-11 h-11 text-sm font-semibold transition-all duration-200
                               text-fuchsia-300 bg-fuchsia-500/10 hover:bg-fuchsia-500/20
                               border border-fuchsia-500/20 hover:border-fuchsia-500/40
                               active:scale-[0.95]
                               border-r-0 border-l-0">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                    </svg>
                </button>

	                <button wire:click="saveStep2"
	                        wire:loading.attr="disabled"
	                        wire:target="saveStep2"
	                        title="{{ $saved ? 'Guardado - sin cambios pendientes' : 'Guardar lección - hay cambios sin guardar' }}"
	                        @class([
	                            'inline-flex items-center justify-center w-11 h-11 rounded-r-xl text-sm font-semibold transition-all duration-200 text-white active:scale-[0.95] disabled:opacity-60 disabled:cursor-not-allowed disabled:active:scale-100',
	                            'shadow-lg shadow-blue-500/20 bg-gradient-to-br from-blue-500 to-blue-600 hover:from-blue-400 hover:to-blue-500 active:shadow-blue-500/30 border border-blue-400/30' => $saved,
	                            'shadow-lg shadow-amber-500/20 bg-gradient-to-br from-amber-500 to-orange-600 hover:from-amber-400 hover:to-orange-500 active:shadow-amber-500/30 border border-amber-400/30' => !$saved,
	                        ])
	                        @disabled($isPublished)>
                    <svg wire:loading wire:target="saveStep2" class="w-4 h-4 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                    </svg>
                    <svg wire:loading.remove wire:target="saveStep2" class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/>
                    </svg>
                </button>
                </div>
            </div>

        @endif
    </div>
</div>
    @endif

    {{-- ═══════════ MODAL VISTA ESTUDIANTE (componente unificado) ═══════════ --}}
    @if($showListStudentPreview && $listPreviewData)
        <x-lms.student-preview :preview="$listPreviewData" closeMethod="closeListStudentPreview" />
    @endif

    @include("livewire.profesor.lms._help-modal")

        {{-- @endif --}}

</div>

{{-- ═══ Mermaid.js — bundled via Vite (resources/js/app.js) ═══ --}}
@include("livewire.profesor.lms._styles")


@include("livewire.profesor.lms._scripts")
