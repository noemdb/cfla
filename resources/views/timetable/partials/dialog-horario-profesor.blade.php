{{-- Diálogo compartido (wizard / light): horario por profesor. --}}
{{-- $titleSuffix: sufijo del id del título ('-light' en el light). --}}

    @if ($showTeacherScheduleDialog)
        <div class="fixed inset-0 z-[60] flex items-center justify-center bg-gray-950/70 p-4"
            role="dialog" aria-modal="true" aria-labelledby="teacher-schedule-title{{ $titleSuffix ?? '' }}"
            x-data x-on:keydown.escape.window="$wire.closeTeacherScheduleDialog()">
            <div x-on:click.stop class="flex max-h-[92vh] w-full max-w-7xl flex-col overflow-hidden rounded-xl border border-gray-200 bg-white shadow-2xl dark:border-white/10 dark:bg-gray-900">
                <div class="flex flex-wrap items-center justify-between gap-3 border-b border-gray-200 px-5 py-4 dark:border-white/10">
                    <div>
                        <h2 id="teacher-schedule-title{{ $titleSuffix ?? '' }}" class="text-sm font-extrabold text-gray-900 dark:text-white">Horario por profesor</h2>
                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Toda la carga horaria asignada al profesor en las secciones de todos los calendarios/P.Estudios del lapso.</p>
                    </div>
                    <button type="button" wire:click="closeTeacherScheduleDialog"
                        class="inline-flex h-8 w-8 items-center justify-center rounded-full text-lg text-gray-500 hover:bg-gray-100 hover:text-gray-900 focus:outline-none focus:ring-2 focus:ring-fuchsia-500/50 dark:hover:bg-white/10 dark:hover:text-white"
                        aria-label="Cerrar horario por profesor">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="border-b border-gray-200 bg-gray-50 px-5 py-3 dark:border-white/10 dark:bg-white/5">
                    <label for="teacher-schedule-profesor" class="mb-1 block text-[10px] font-extrabold uppercase tracking-widest text-gray-500 dark:text-gray-400">Profesor</label>
                    <select id="teacher-schedule-profesor" wire:model.live="teacherScheduleProfesorId"
                        class="w-full max-w-xl rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 outline-none focus:border-fuchsia-500 focus:ring-2 focus:ring-fuchsia-500/40 dark:border-white/10 dark:bg-gray-900 dark:text-white">
                        @forelse ($teacherScheduleOptions as $teacher)
                            <option value="{{ $teacher['id'] }}">{{ $teacher['name'] }}</option>
                        @empty
                            <option value="">No hay profesores asociados</option>
                        @endforelse
                    </select>
                </div>
                @if ($teacherScheduleCalendars !== [])
                    <div class="flex border-b border-gray-200 bg-gray-50 dark:border-white/10 dark:bg-white/5"
                        role="tablist" aria-label="P.Estudios del horario docente">
                        @foreach ($teacherScheduleCalendars as $cal)
                            @php $tabActive = (int) ($teacherScheduleActiveCalendarId ?? 0) === (int) $cal['calendar_id']; @endphp
                            <button type="button" role="tab" aria-selected="{{ $tabActive ? 'true' : 'false' }}"
                                wire:click="setTeacherScheduleTab({{ $cal['calendar_id'] }})"
                                title="{{ $cal['calendar'] }}"
                                class="flex-1 basis-0 border-b-2 px-3 py-2 text-center text-xs font-bold transition-colors {{ $tabActive ? 'border-fuchsia-500 text-fuchsia-700 dark:text-fuchsia-300' : 'border-transparent text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200' }}">
                                {{ $cal['pestudio'] }}
                            </button>
                        @endforeach
                    </div>
                @endif
                <div class="overflow-auto p-5">
                    @if ($teacherScheduleHasAssignments)
                        <table class="w-full min-w-[900px] table-fixed border-collapse text-left text-xs">
                            <caption class="sr-only">Lessons del profesor seleccionado por día y bloque</caption>
                            <thead>
                                <tr class="border-b border-gray-200 bg-gray-50 text-[10px] font-extrabold uppercase tracking-widest text-gray-500 dark:border-white/10 dark:bg-white/5 dark:text-gray-400">
                                    <th class="w-36 px-3 py-2.5">Turno · bloque</th>
                                    @foreach (['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes'] as $dayLabel)
                                        <th class="px-3 py-2.5 text-center">{{ $dayLabel }}</th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 dark:divide-white/5">
                                @foreach ($teacherScheduleGrid as $row)
                                    <tr>
                                        <th scope="row" class="px-3 py-2.5 font-bold text-gray-700 dark:text-gray-200">
                                            <span class="block">{{ $row['shift'] }}</span>
                                            <span class="mt-0.5 block text-[9px] font-normal uppercase tracking-widest text-gray-500 dark:text-gray-400">{{ $row['code'] }} · #{{ $row['order'] }}</span>
                                        </th>
                                        @for ($day = 1; $day <= 5; $day++)
                                            <td class="px-2 py-2 align-top">
                                                @forelse ($row['cells'][$day] ?? [] as $lesson)
                                                    <div class="mb-1 rounded-md border border-fuchsia-500/20 bg-fuchsia-500/5 p-2 last:mb-0">
                                                        <div class="flex items-start justify-between gap-1">
                                                            <div class="font-bold text-gray-900 dark:text-white">{{ $lesson['subject'] }}</div>
                                                            @if (!empty($lesson['pestudio_code']))
                                                                <span class="shrink-0 rounded bg-fuchsia-500/10 px-1 py-0.5 text-[9px] font-bold uppercase tracking-wide text-fuchsia-700 dark:text-fuchsia-300">{{ $lesson['pestudio_code'] }}</span>
                                                            @endif
                                                        </div>
                                                        <div class="mt-0.5 font-mono text-[10px] text-gray-500 dark:text-gray-400">{{ $lesson['start'] }}–{{ $lesson['end'] }}</div>
                                                        <div class="mt-0.5 text-[10px] text-gray-500 dark:text-gray-400">
                                                            @if (!empty($lesson['grado'])){{ $lesson['grado'] }} · @endif Sección {{ $lesson['section'] }}
                                                        </div>
                                                        <div class="mt-0.5 text-[10px] text-gray-400 dark:text-gray-500">Lesson #{{ $lesson['lesson_id'] }}</div>
                                                    </div>
                                                @empty
                                                    <span class="text-gray-300 dark:text-gray-700">—</span>
                                                @endforelse
                                            </td>
                                        @endfor
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @else
                        <div class="rounded-lg border border-dashed border-gray-300 px-4 py-10 text-center text-sm text-gray-500 dark:border-white/10 dark:text-gray-400">
                            El profesor seleccionado no tiene lessons asignadas en este P.Estudio.
                        </div>
                    @endif
                </div>
            </div>
        </div>
    @endif