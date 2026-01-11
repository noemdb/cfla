<div>

    @if ($debates->isNotEmpty())

        <div class="md:flex my-2 gap-4">
            <ul class="flex-column space-y-3 text-sm font-medium md:me-4 mb-4 md:mb-0 min-w-[250px]">
                <li
                    class="bg-emerald-600/20 border border-emerald-500/30 text-emerald-400 py-3 px-4 rounded-xl font-black uppercase tracking-widest text-center shadow-lg backdrop-blur-md">
                    {{ $grado->name }}
                </li>
                @foreach ($debates as $item)
                    <li class="group">
                        <div class="flex items-center gap-2">
                            <button type="button" wire:click="active({{ $item->id }})"
                                class="grow flex items-center justify-between px-4 py-4 rounded-xl border transition-all duration-300
                                {{ $item->id == $active_id
                                    ? 'bg-emerald-600 border-emerald-400 text-white shadow-emerald-500/20 shadow-lg'
                                    : 'bg-gray-800/40 border-emerald-500/10 text-gray-300 hover:border-emerald-500/40 hover:bg-gray-800/60' }}">
                                <div class="text-start">
                                    <div class="font-bold uppercase tracking-tight">{{ $item->name }}</div>
                                    <div
                                        class="text-[10px] {{ $item->id == $active_id ? 'text-emerald-200' : 'text-gray-500' }} font-bold">
                                        {{ $item->full_grado }}</div>
                                </div>
                                @if ($item->status_active)
                                    <div class="flex h-2 w-2 relative">
                                        <span
                                            class="animate-ping absolute inline-flex h-full w-full rounded-full bg-white opacity-75"></span>
                                        <span class="relative inline-flex rounded-full h-2 w-2 bg-white"></span>
                                    </div>
                                @endif
                            </button>

                            <button type="button" wire:click="activeOnline({{ $item->id }})"
                                class="shrink-0 p-4 rounded-xl transition-all duration-300 border
                                {{ $item->status_active ? 'bg-red-500/20 border-red-500/30 text-red-400 hover:bg-red-500/40' : 'bg-emerald-500/20 border-emerald-500/30 text-emerald-400 hover:bg-emerald-500/40' }}"
                                title="{{ $item->status_active ? 'Desactivar' : 'Activar' }}">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    @if ($item->status_active)
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M10 9v6m4-6v6m7-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    @else
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    @endif
                                </svg>
                            </button>
                        </div>
                    </li>
                @endforeach
            </ul>

            <div
                class="diagnostic-card border border-emerald-500/20 rounded-2xl p-6 w-full shadow-2xl backdrop-blur-xl">
                @if ($active_id)
                    @php $key = "competition-moderator-question-component-".$active_id; @endphp
                    <livewire:app.general.educational.competition.moderator.question-component :debate_id="$active_id" />
                @else
                    <div>Seleccione debate</div>
                @endif
            </div>

        </div>
    @else
        @if ($grado_id)
            <div>No hay debates registrados</div>
        @else
            <div>Seleccione Grado/Año</div>
        @endif
    @endif
</div>
