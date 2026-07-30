{{-- Partial: Jornadas del Censo --}}
{{-- Controlado por $modalJornadas en CatchmentWizard --}}
<div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm"
    wire:click.self="closeJornadasInfo"
    x-transition:enter="transition ease-out duration-300"
    x-transition:enter-start="opacity-0"
    x-transition:enter-end="opacity-100"
    x-transition:leave="transition ease-in duration-200"
    x-transition:leave-start="opacity-100"
    x-transition:leave-end="opacity-0">

    <div class="relative w-full max-w-lg rounded-2xl bg-white dark:bg-gray-900 shadow-2xl shadow-black/20 overflow-hidden"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 scale-95 translate-y-4"
        x-transition:enter-end="opacity-100 scale-100 translate-y-0"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100 scale-100 translate-y-0"
        x-transition:leave-end="opacity-0 scale-95 translate-y-4">

        {{-- Header --}}
        <div class="flex items-center justify-between px-6 pt-5 pb-3">
            <div class="flex items-center gap-3">
                <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-gradient-to-br from-sky-400 to-sky-600 text-white shadow-lg shadow-sky-500/20">
                    <x-icon name="calendar-days" class="w-5 h-5" />
                </span>
                <div>
                    <h3 class="text-base font-bold text-gray-900 dark:text-white">Jornadas del Censo</h3>
                    <p class="text-[11px] text-gray-500 dark:text-gray-400 mt-0.5">Convocatorias disponibles para agendar tu cita</p>
                </div>
            </div>
            <button wire:click="closeJornadasInfo"
                class="shrink-0 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition rounded-xl p-1.5 hover:bg-gray-100 dark:hover:bg-gray-800">
                <x-icon name="x-mark" class="w-5 h-5" />
            </button>
        </div>

        {{-- Body --}}
        <div class="px-6 pb-5 space-y-4 text-gray-700 dark:text-gray-200">

            {{-- Horario banner --}}
            <div class="flex items-center gap-3 rounded-xl bg-gradient-to-br from-sky-50 to-blue-50 dark:from-sky-950/40 dark:to-blue-950/30 border border-sky-200/60 dark:border-sky-800/40 px-4 py-3 shadow-sm">
                <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-white dark:bg-gray-800 text-sky-600 dark:text-sky-400 shadow-sm">
                    <x-icon name="clock" class="w-5 h-5" />
                </span>
                <div>
                    <p class="font-semibold text-sky-800 dark:text-sky-300 text-sm leading-snug">
                        Horario de atención: <span class="font-bold">8:00 AM a 12:00 M</span>
                    </p>
                    <p class="text-[11px] text-sky-600/70 dark:text-sky-400/60 mt-0.5">Lunes a viernes — presentarse con el estudiante</p>
                </div>
            </div>

            {{-- Lista de jornadas --}}
            <div class="rounded-xl border border-gray-200/60 dark:border-gray-700/50 bg-gray-50/80 dark:bg-gray-800/30 p-1 max-h-72 overflow-y-auto [&::-webkit-scrollbar]:w-1 [&::-webkit-scrollbar-thumb]:rounded-full [&::-webkit-scrollbar-thumb]:bg-gray-300 dark:[&::-webkit-scrollbar-thumb]:bg-gray-700 [&::-webkit-scrollbar-track]:bg-transparent">

                @php
                    $jornadas = \App\Models\app\Academy\Catchment::JORNADAS;
                    $today = now()->toDateString();
                @endphp

                <div class="space-y-0.5">
                    @foreach($jornadas as $jornada)
                        @php
                            $isActive = $today >= $jornada['start'] && $today <= $jornada['end'];
                            $isPast = $today > $jornada['end'];
                        @endphp
                        <div class="group flex items-center gap-3 px-3 py-2.5 rounded-lg transition-all duration-200
                            {{ $isActive ? 'bg-white dark:bg-gray-800/80 ring-1 ring-sky-400/30 shadow-sm' : 'hover:bg-white/50 dark:hover:bg-gray-800/40' }}">
                            {{-- Indicador visual --}}
                            <div class="flex-shrink-0 relative">
                                <div class="w-2.5 h-2.5 rounded-full transition-all duration-300
                                    {{ $isActive ? 'bg-sky-500 ring-4 ring-sky-500/20' : ($isPast ? 'bg-gray-300 dark:bg-gray-600' : 'bg-amber-400 dark:bg-amber-500') }}">
                                </div>
                                {{-- Línea conectora (excepto último) --}}
                                @if(!$loop->last)
                                    <div class="absolute top-3.5 left-1.125 w-px h-[calc(100%+8px)] {{ $isActive ? 'bg-sky-200 dark:bg-sky-800' : 'bg-gray-200 dark:bg-gray-700' }}"></div>
                                @endif
                            </div>

                            {{-- Texto --}}
                            <div class="flex-1 min-w-0">
                                <p class="text-sm leading-snug truncate
                                    {{ $isActive ? 'font-semibold text-gray-900 dark:text-white' : ($isPast ? 'text-gray-400 dark:text-gray-500' : 'text-gray-700 dark:text-gray-300') }}">
                                    {{ $jornada['label'] }}
                                </p>
                            </div>

                            {{-- Badge --}}
                            <div class="flex-shrink-0">
                                @if($isActive)
                                    <span class="inline-flex items-center gap-1 text-[10px] font-bold uppercase tracking-wider text-sky-600 dark:text-sky-400 bg-sky-50 dark:bg-sky-900/40 px-2.5 py-1 rounded-full border border-sky-200/50 dark:border-sky-700/40">
                                        <span class="w-1.5 h-1.5 rounded-full bg-sky-500 animate-pulse"></span>
                                        Activa
                                    </span>
                                @elseif(!$isPast)
                                    <span class="inline-flex items-center gap-1 text-[10px] font-bold uppercase tracking-wider text-amber-600 dark:text-amber-400 bg-amber-50 dark:bg-amber-900/30 px-2.5 py-1 rounded-full border border-amber-200/50 dark:border-amber-700/30">
                                        Próxima
                                    </span>
                                @else
                                    <span class="text-[10px] font-medium text-gray-400 dark:text-gray-600">Completada</span>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- Nota al pie --}}
            <p class="text-[11px] text-gray-400 dark:text-gray-500 text-center leading-relaxed">
                * Las jornadas se realizan en la sede del colegio, en el horario indicado.
                <br class="hidden sm:block">Debe asistir acompañado del estudiante.
            </p>
        </div>

        {{-- Footer --}}
        <div class="flex justify-end px-6 py-3 bg-gray-50/80 dark:bg-gray-800/20 border-t border-gray-100 dark:border-gray-800">
            <button wire:click="closeJornadasInfo"
                class="inline-flex items-center gap-2 px-5 py-2.5 min-h-[40px] text-sm font-bold text-white bg-gradient-to-br from-sky-500 to-sky-600 hover:from-sky-600 hover:to-sky-700 rounded-xl shadow-lg shadow-sky-500/20 hover:shadow-sky-500/30 transition-all duration-200 active:scale-[0.97]">
                Entendido
            </button>
        </div>
    </div>
</div>
