<div class="fade-in">
    <div class="mb-10 flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-3xl font-extrabold text-white mb-2">Administración de Diagnóstico</h1>
            <p class="text-emerald-400 font-medium">Gestiona la activación de pensums para las evaluaciones diagnósticas.</p>
        </div>
        <a href="{{ route('admin.index') }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-white/5 hover:bg-white/10 text-gray-300 rounded-xl border border-white/5 transition-all duration-300 text-sm font-bold">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            Volver al Panel
        </a>
    </div>

    @foreach($pestudios as $pestudio)
        <div class="mb-12" wire:key="pestudio-{{ $pestudio->id }}">
            @php
                $activeCount = collect($groupedPensums[$pestudio->id] ?? [])
                    ->flatten(1)
                    ->where('status_active_diagnostic', true)
                    ->count();
            @endphp

            <div class="flex items-center justify-between mb-6 group">
                <div class="flex items-center gap-4">
                    <div class="w-10 h-10 bg-emerald-500/20 rounded-xl flex items-center justify-center group-hover:bg-emerald-500/30 transition-colors">
                        <svg class="w-5 h-5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-xl font-bold text-white">{{ $pestudio->name }}</h2>
                        <p class="text-gray-400 text-xs uppercase tracking-widest">{{ $pestudio->code }}</p>
                    </div>
                </div>

                <div class="flex items-center gap-3">
                    @if($activeCount > 0)
                        <x-button 
                            wire:click="toggleAllPestudio({{ $pestudio->id }}, false)"
                            label="Desactivar Todo"
                            icon="eye-slash"
                            secondary
                            outline
                            xs
                            rounded="xl"
                        />
                    @endif
                    <x-button 
                        wire:click="toggleAllPestudio({{ $pestudio->id }}, true)"
                        label="Activar Todo"
                        icon="eye"
                        emerald
                        outline
                        xs
                        rounded="xl"
                    />

                    @if($activeCount > 0)
                        <div class="flex items-center gap-3 px-4 py-2 bg-emerald-500/10 border border-emerald-500/20 rounded-2xl backdrop-blur-sm">
                            <div class="flex flex-col items-end">
                                <span class="text-[10px] text-emerald-500 font-bold uppercase tracking-widest">Activos</span>
                                <span class="text-lg font-black text-white leading-none">{{ $activeCount }}</span>
                            </div>
                            <div class="w-8 h-8 rounded-lg bg-emerald-500 flex items-center justify-center shadow-[0_0_15px_rgba(16,185,129,0.4)]">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
                @foreach($pestudio->getGradosActive() as $grado)
                    @php 
                        $pensums = $groupedPensums[$pestudio->id][$grado->id] ?? collect();
                        $gradeActiveCount = $pensums->where('status_active_diagnostic', true)->count();
                    @endphp

                    <div class="bg-gray-900/40 backdrop-blur-md border border-white/5 p-5 rounded-2xl overflow-hidden transition-all duration-300 hover:border-emerald-500/20" wire:key="grado-{{ $pestudio->id }}-{{ $grado->id }}">
                        <div class="flex items-center justify-between mb-4 pb-4 border-b border-white/5">
                            <h3 class="font-bold text-emerald-100">{{ $grado->name }}</h3>
                            <div class="flex flex-col items-end gap-2">
                                <div class="flex items-center gap-2">
                                    @if($gradeActiveCount > 0)
                                        <span class="px-2 py-0.5 bg-emerald-500 text-[9px] text-white font-bold rounded-full shadow-[0_0_10px_rgba(16,185,129,0.3)]">
                                            {{ $gradeActiveCount }} Activos
                                        </span>
                                    @endif
                                </div>
                                <div class="flex items-center gap-2">
                                    @if($gradeActiveCount > 0)
                                        <x-button 
                                            wire:click="toggleAllGrado({{ $pestudio->id }}, {{ $grado->id }}, false)"
                                            icon="eye-slash"
                                            secondary
                                            outline
                                            2xs
                                            rounded="lg"
                                            title="Desactivar Todo el Grado"
                                        />
                                    @endif
                                    <x-button 
                                        wire:click="toggleAllGrado({{ $pestudio->id }}, {{ $grado->id }}, true)"
                                        icon="eye"
                                        emerald
                                        outline
                                        2xs
                                        rounded="lg"
                                        title="Activar Todo el Grado"
                                    />
                                </div>
                            </div>
                        </div>

                        <div class="space-y-3">

                            @forelse($pensums as $pensum)
                                <div class="flex items-center justify-between group/item p-3 rounded-xl transition-all duration-300 {{ $pensum->status_active_diagnostic ? 'bg-emerald-500/10 border border-emerald-500/20' : 'bg-transparent border border-transparent' }}" wire:key="pensum-{{ $pensum->id }}">
                                    <div class="flex flex-col">
                                        <div class="flex items-center gap-2">
                                            <span class="text-sm text-gray-300 font-medium group-hover/item:text-white transition-colors">
                                                {{ $pensum->asignatura->name }}
                                            </span>
                                            @if($pensum->status_active_diagnostic)
                                                <span class="flex items-center gap-1 px-1.5 py-0.5 rounded-md bg-emerald-500/20 text-[9px] font-bold text-emerald-400 border border-emerald-500/20 uppercase tracking-tighter animate-pulse">
                                                    <svg class="w-2.5 h-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                                    </svg>
                                                    Activo
                                                </span>
                                            @endif
                                        </div>
                                        <span class="text-[10px] text-gray-500">{{ $pensum->asignatura->code }}</span>
                                    </div>

                                    <x-button 
                                        wire:click="toggleStatus({{ $pensum->id }})" 
                                        :icon="$pensum->status_active_diagnostic ? 'eye' : 'eye-slash'"
                                        :emerald="$pensum->status_active_diagnostic"
                                        :secondary="!$pensum->status_active_diagnostic"
                                        outline 
                                        xs 
                                        rounded="lg"
                                        wire:loading.attr="disabled"
                                    />
                                </div>
                            @empty
                                <div class="flex items-center justify-center p-4 bg-white/2 inset-0 text-center">
                                    <p class="text-xs text-gray-600 italic">No hay asignaturas vinculadas al pensum</p>
                                </div>
                            @endforelse
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endforeach
</div>
