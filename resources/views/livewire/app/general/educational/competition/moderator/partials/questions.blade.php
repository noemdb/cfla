<div class="text-sm font-medium">
    <div class="grid grid-cols-1 md:grid-cols-12 gap-6">
        <div class="col-span-12 lg:col-span-5 text-start">
            <div class="flex items-center justify-between mb-4 border-b border-emerald-500/10 pb-2">
                <span class="text-xs font-black uppercase tracking-widest text-emerald-500/60">Banco de
                    Interrogantes</span>
                <span class="text-[10px] text-gray-500 font-bold">{{ $questions->count() }} Disponibles</span>
            </div>

            <ul class="space-y-3 max-h-[600px] overflow-y-auto pr-2 custom-scrollbar">
                @forelse ($questions->sortBy('category') as $item)
                    @php $active = ($item->id == $active_id) ? true : false; @endphp
                    <li class="group">
                        <button wire:click="active({{ $item->id }})"
                            class="w-full text-start p-4 rounded-xl border transition-all duration-300 relative overflow-hidden
                            {{ $active
                                ? 'bg-emerald-600 border-emerald-400 text-white shadow-lg shadow-emerald-500/20'
                                : 'bg-gray-900/40 border-emerald-500/10 text-gray-300 hover:border-emerald-500/30 hover:bg-gray-900/60' }}">

                            @if ($active)
                                <div class="absolute top-0 right-0 p-1 bg-white/20 rounded-bl-lg">
                                    <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M5 13l4 4L19 7" />
                                    </svg>
                                </div>
                            @endif

                            <div class="flex items-start gap-3">
                                <span
                                    class="text-xs font-black {{ $active ? 'text-emerald-200' : 'text-emerald-500/60' }}">{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}.</span>
                                <div class="flex-1">
                                    <div
                                        class="font-bold leading-tight mb-2 {{ $active ? 'text-white' : 'group-hover:text-emerald-300' }} transition-colors">
                                        {!! $item->text !!}
                                    </div>
                                    <div
                                        class="flex items-center gap-3 pt-2 border-t {{ $active ? 'border-white/10 text-emerald-100' : 'border-emerald-500/5 text-gray-500' }} text-[10px] font-bold uppercase tracking-widest">
                                        <span>{{ $item->category }}</span>
                                        <span class="w-1 h-1 bg-current rounded-full opacity-30"></span>
                                        <span>{{ $item->time }}s</span>
                                        <span class="w-1 h-1 bg-current rounded-full opacity-30"></span>
                                        <span>{{ $item->weighting }}pts</span>
                                    </div>
                                </div>
                                <div class="shrink-0 flex gap-1">
                                    @if ($item->status_active)
                                        <span class="h-2 w-2 rounded-full bg-red-400 animate-pulse"
                                            title="En curso"></span>
                                    @endif
                                    @if ($item->status_answer)
                                        <svg class="w-3 h-3 {{ $active ? 'text-white' : 'text-emerald-400' }}"
                                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M5 13l4 4L19 7" />
                                        </svg>
                                    @endif
                                </div>
                            </div>
                        </button>
                    </li>
                @empty
                    <li
                        class="py-8 text-center text-gray-600 italic border border-dashed border-emerald-500/10 rounded-xl">
                        No hay preguntas registradas
                    </li>
                @endforelse
            </ul>
        </div>
        <div class="col-span-12 lg:col-span-7">
            @if ($active_id)
                @php $key = "competition-moderator-option-component-".$active_id; @endphp
                <div class="bg-gray-900/20 rounded-2xl p-6 border border-emerald-500/10">
                    <livewire:app.general.educational.competition.moderator.option-component :question_id="$active_id" />
                </div>
            @else
                <div
                    class="h-full flex flex-col items-center justify-center py-20 bg-gray-900/20 rounded-2xl border border-dashed border-emerald-500/10 opacity-50">
                    <svg class="w-16 h-16 text-emerald-500/20 mb-4" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M15 15l-2 5L9 9l11 4-5 2zm0 0l5 5M7.188 2.239l.777 2.897M5.136 7.965l-2.898-.777M13.95 4.05l-2.122 2.122m-5.657 5.656l-2.12 2.122" />
                    </svg>
                    <p class="text-sm font-bold uppercase tracking-widest text-emerald-500/40">Seleccione una pregunta
                        para gestionar sus opciones</p>
                </div>
            @endif
        </div>
    </div>
</div>
