<div class="fade-in" x-data="timetableEditor()">
    {{-- Header --}}
    <div class="mb-6 sm:mb-8 flex flex-col md:flex-row md:items-center justify-between gap-3">
        <div>
            <h1 class="text-lg font-extrabold text-gray-900 dark:text-white mb-2">Editor de Horario</h1>
            <p class="text-emerald-600 dark:text-emerald-400 font-medium text-sm">Arrastra y suelta bloques para ajustar el horario</p>
        </div>
        <div class="flex items-center gap-2">
            <button wire:click="$refresh"
                class="inline-flex items-center gap-2 px-3 py-2 min-h-[44px] min-w-[44px] bg-white/5 hover:bg-white/10 text-gray-300 rounded-lg border border-white/5 transition-all duration-300 text-sm font-bold">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                <span class="hidden sm:inline">Refrescar</span>
            </button>
        </div>
    </div>

    {{-- Filters --}}
    <div class="bg-white dark:bg-gray-900/40 backdrop-blur-md border border-gray-200 dark:border-white/5 rounded-lg p-4 mb-6">
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
            <div>
                <label class="block text-[10px] font-bold uppercase tracking-widest text-gray-500 dark:text-gray-400 mb-1.5">Calendario</label>
                <select wire:model.live="calendarId" class="w-full bg-white dark:bg-white/5 border border-gray-200 dark:border-white/10 text-gray-900 dark:text-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500/50 outline-none">
                    <option value="">Selecciona un calendario</option>
                    @foreach ($calendars as $cal)
                        <option value="{{ $cal->id }}">{{ $cal->name }} ({{ $cal->status }})</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-[10px] font-bold uppercase tracking-widest text-gray-500 dark:text-gray-400 mb-1.5">Vista</label>
                <select wire:model.live="view" class="w-full bg-white dark:bg-white/5 border border-gray-200 dark:border-white/10 text-gray-900 dark:text-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500/50 outline-none">
                    <option value="section">Por sección</option>
                    <option value="teacher">Por docente</option>
                </select>
            </div>
            <div>
                <label class="block text-[10px] font-bold uppercase tracking-widest text-gray-500 dark:text-gray-400 mb-1.5">Filtro</label>
                <select wire:model.live="{{ $view === 'section' ? 'sectionFilterId' : 'teacherFilterId' }}" class="w-full bg-white dark:bg-white/5 border border-gray-200 dark:border-white/10 text-gray-900 dark:text-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500/50 outline-none">
                    <option value="">Todas</option>
                    @if ($view === 'section')
                        @foreach ($sections as $section)
                            <option value="{{ $section->id }}">{{ $section->name }}</option>
                        @endforeach
                    @else
                        @foreach ($teachers as $teacher)
                            <option value="{{ $teacher->id }}">{{ $teacher->lastname }}, {{ $teacher->name }}</option>
                        @endforeach
                    @endif
                </select>
            </div>
        </div>
    </div>

    {{-- Conflict message --}}
    @if ($conflictMessage)
        <div class="mb-4 px-4 py-3 rounded-lg bg-red-500/10 border border-red-500/30 text-red-600 dark:text-red-400 text-sm font-medium" x-transition>
            {{ $conflictMessage }}
        </div>
    @endif

    {{-- Unplaced lessons tray --}}
    @if ($unplacedLessons->isNotEmpty())
        <div class="bg-white dark:bg-gray-900/40 backdrop-blur-md border border-gray-200 dark:border-white/5 rounded-lg p-4 mb-6">
            <h2 class="text-sm font-extrabold text-gray-900 dark:text-white mb-3">Sin ubicar (arrastra al período)</h2>
            <div class="flex flex-wrap gap-2">
                @foreach ($unplacedLessons as $lesson)
                    <div draggable="true"
                        @dragstart="onLessonDragStart($event, {{ $lesson->id }})"
                        class="px-3 py-1.5 rounded-lg bg-emerald-500/10 border border-emerald-500/30 text-emerald-600 dark:text-emerald-400 text-xs font-bold cursor-grab active:cursor-grabbing select-none">
                        {{ $lesson->pevaluacion?->pensum?->asignatura?->name }} · {{ $lesson->pevaluacion?->seccion?->name }}
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    {{-- Grid --}}
    @if ($calendar)
        <div class="bg-white dark:bg-gray-900/40 backdrop-blur-md border border-gray-200 dark:border-white/5 rounded-lg overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-gray-200 dark:border-white/10">
                            <th class="px-3 py-2 text-left text-[10px] font-bold uppercase tracking-widest text-gray-500 dark:text-gray-400 w-24">Período</th>
                            @foreach (['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes'] as $day)
                                <th class="px-3 py-2 text-center text-[10px] font-bold uppercase tracking-widest text-gray-500 dark:text-gray-400">{{ $day }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($periods->groupBy('order_in_day') as $order => $group)
                            <tr class="border-b border-gray-100 dark:border-white/5">
                                <td class="px-3 py-2 text-xs font-bold text-gray-500 dark:text-gray-400">
                                    {{ $order }}º
                                    <span class="block text-[10px] font-normal text-gray-400 dark:text-gray-500">{{ optional($group->first()->shift)->code }}</span>
                                </td>
                                @foreach ($group as $period)
                                    @php
                                        $cellSlots = $slots->where('period_id', $period->id);
                                    @endphp
                                    <td class="px-1 py-1 min-h-[70px] align-top border-l border-gray-100 dark:border-white/5"
                                        @dragover.prevent @drop.prevent="onDrop($event, {{ $period->id }})">
                                        @foreach ($cellSlots as $slot)
                                            <div draggable="true"
                                                @dragstart="onSlotDragStart($event, {{ $slot->id }})"
                                                class="mb-1 px-2 py-1.5 rounded-md text-xs font-bold select-none cursor-grab active:cursor-grabbing {{ $slot->locked ? 'bg-amber-500/20 border border-amber-500/40 text-amber-600 dark:text-amber-400' : 'bg-sky-500/10 border border-sky-500/30 text-sky-600 dark:text-sky-400' }}">
                                                <div class="truncate">{{ $slot->lesson?->pevaluacion?->pensum?->asignatura?->name ?? '?' }}</div>
                                                <div class="truncate text-[10px] font-normal text-gray-500 dark:text-gray-400">
                                                    {{ $view === 'section' ? ($slot->lesson?->pevaluacion?->profesor?->lastname ?? '?') : ($slot->lesson?->pevaluacion?->seccion?->name ?? '?') }}
                                                </div>
                                                @if ($slot->room_id)
                                                    <div class="text-[10px] font-normal text-gray-400">Aula</div>
                                                @endif
                                            </div>
                                        @endforeach
                                    </td>
                                @endforeach
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @else
        <div class="bg-white dark:bg-gray-900/40 backdrop-blur-md border border-gray-200 dark:border-white/5 rounded-lg p-8 text-center text-sm text-gray-500 dark:text-gray-400">
            @if ($calendars->isEmpty())
                Selecciona un calendario para editar.
            @else
                Selecciona un calendario para editar:
                <div class="mt-4 flex flex-wrap justify-center gap-2">
                    @foreach ($calendars as $cal)
                        <button wire:click="$set('calendarId', {{ $cal->id }})"
                            class="px-4 py-2 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold">
                            {{ $cal->name }} ({{ $cal->status }})
                        </button>
                    @endforeach
                </div>
            @endif
        </div>
    @endif

    @push('scripts')
    <script>
        function timetableEditor() {
            return {
                dragSlotId: null,
                dragLessonId: null,
                onSlotDragStart(event, slotId) {
                    this.dragSlotId = slotId;
                    this.dragLessonId = null;
                    event.dataTransfer.effectAllowed = 'move';
                    event.dataTransfer.setData('text/plain', 'slot:' + slotId);
                },
                onLessonDragStart(event, lessonId) {
                    this.dragLessonId = lessonId;
                    this.dragSlotId = null;
                    event.dataTransfer.effectAllowed = 'move';
                    event.dataTransfer.setData('text/plain', 'lesson:' + lessonId);
                },
                onDrop(event, periodId) {
                    const data = event.dataTransfer.getData('text/plain');
                    if (data.startsWith('slot:')) {
                        const slotId = parseInt(data.split(':')[1], 10);
                        if (this.dragSlotId !== null) {
                            @this.call('moveSlot', slotId, periodId);
                        }
                    } else if (data.startsWith('lesson:')) {
                        const lessonId = parseInt(data.split(':')[1], 10);
                        @this.call('dropLesson', lessonId, periodId);
                    }
                    this.dragSlotId = null;
                    this.dragLessonId = null;
                },
            };
        }
    </script>
    @endpush
</div>