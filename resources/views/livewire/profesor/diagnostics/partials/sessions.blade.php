{{-- Sessions Tab --}}
<div class="space-y-4">
    {{-- Stats Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
        <div class="bg-gray-800/30 border border-white/5 rounded-lg p-4">
            <div class="flex items-center justify-between mb-1">
                <span class="text-[10px] font-bold uppercase tracking-widest text-gray-500">Estudiantes</span>
                <svg class="w-4 h-4 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"></path>
                </svg>
            </div>
            <p class="text-lg font-bold text-white">{{ $stats['total_students'] ?? 0 }}</p>
        </div>
        <div class="bg-gray-800/30 border border-white/5 rounded-lg p-4">
            <div class="flex items-center justify-between mb-1">
                <span class="text-[10px] font-bold uppercase tracking-widest text-gray-500">Con sesión</span>
                <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
            <p class="text-lg font-bold text-white">{{ $stats['students_with_sessions'] ?? 0 }}</p>
        </div>
        <div class="bg-gray-800/30 border border-white/5 rounded-lg p-4">
            <div class="flex items-center justify-between mb-1">
                <span class="text-[10px] font-bold uppercase tracking-widest text-gray-500">Sin sesión</span>
                <svg class="w-4 h-4 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                </svg>
            </div>
            <p class="text-lg font-bold text-white">{{ ($stats['total_students'] ?? 0) - ($stats['students_with_sessions'] ?? 0) }}</p>
        </div>
    </div>

    {{-- Search & Date Filter --}}
    <div class="flex flex-wrap items-center gap-3 pb-4 border-b border-white/5">
        <div class="relative flex-1 min-w-[200px] max-w-xs">
            <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-3.5 h-3.5 text-gray-500 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
            </svg>
            <input type="text" wire:model.live.debounce.300ms="searchSessions" placeholder="Buscar estudiante..."
                class="w-full bg-gray-800/50 border border-white/10 rounded-lg pl-9 pr-3 py-1.5 text-xs text-gray-300 placeholder-gray-600 focus:border-purple-500/50 focus:ring-1 focus:ring-purple-500/20 transition-all duration-200">
        </div>

        <input type="date" wire:model.live="filterDateFrom"
            class="bg-gray-800/50 border border-white/10 rounded-lg px-3 py-1.5 text-xs text-gray-300 focus:border-purple-500/50 transition-all duration-200">

        <input type="date" wire:model.live="filterDateTo"
            class="bg-gray-800/50 border border-white/10 rounded-lg px-3 py-1.5 text-xs text-gray-300 focus:border-purple-500/50 transition-all duration-200">

        <button wire:click="resetSessionFilters"
            class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-bold bg-gray-700/50 text-gray-300 hover:bg-gray-700 border border-white/10 transition-all duration-200">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
            </svg>
            Limpiar
        </button>
    </div>

    {{-- Sessions Table --}}
    <div class="overflow-x-auto">
        <table class="w-full text-left">
            <thead>
                <tr class="border-b border-white/5">
                    <th class="py-2 px-4 text-[10px] font-bold uppercase tracking-widest text-gray-500">Estudiante</th>
                    <th class="py-2 px-4 text-[10px] font-bold uppercase tracking-widest text-gray-500">Grado</th>
                    <th class="py-2 px-4 text-[10px] font-bold uppercase tracking-widest text-gray-500">Diagnóstico</th>
                    <th class="py-2 px-4 text-[10px] font-bold uppercase tracking-widest text-gray-500">Inicio</th>
                    <th class="py-2 px-4 text-[10px] font-bold uppercase tracking-widest text-gray-500">Estado</th>
                    <th class="py-2 px-4 text-[10px] font-bold uppercase tracking-widest text-gray-500">Aciertos</th>
                    <th class="py-2 px-4 text-[10px] font-bold uppercase tracking-widest text-gray-500 text-right">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($sessions as $session)
                    <tr class="border-b border-white/5 hover:bg-white/[0.02] transition-colors">
                        <td class="py-2 px-4">
                            <div class="flex items-center gap-2">
                                <div class="w-7 h-7 rounded-lg bg-gradient-to-br from-purple-500/20 to-blue-500/20 flex items-center justify-center border border-white/5">
                                    <span class="text-[10px] font-bold text-purple-400">{{ strtoupper(substr($session->estudiant?->full_name ?? '?', 0, 2)) }}</span>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-300 font-medium">{{ $session->estudiant?->full_name ?? 'N/A' }}</p>
                                    <span class="text-[10px] text-gray-600">{{ $session->estudiant?->email ?? '' }}</span>
                                </div>
                            </div>
                        </td>
                        <td class="py-2 px-4">
                            <span class="text-xs text-gray-400">{{ $session->estudiant?->grado?->name ?? '—' }}</span>
                        </td>
                        <td class="py-2 px-4">
                            <span class="text-xs text-gray-400">{{ $session->diagMain?->name ?? '—' }}</span>
                        </td>
                        <td class="py-2 px-4">
                            <span class="text-xs text-gray-400">{{ $session->iniciado_at ? \Carbon\Carbon::parse($session->iniciado_at)->format('d/m/Y H:i') : '—' }}</span>
                        </td>
                        <td class="py-2 px-4">
                            @if($session->completado_at)
                                <span class="inline-flex items-center gap-1 text-xs text-emerald-400">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span>
                                    Completado
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 text-xs text-amber-400">
                                    <span class="w-1.5 h-1.5 rounded-full bg-amber-400"></span>
                                    En progreso
                                </span>
                            @endif
                        </td>
                        <td class="py-2 px-4">
                            @if($session->completado_at && $session->answers_count > 0)
                                @php
                                    $correct = $session->answers->where('is_correct', 1)->count();
                                    $total = $session->answers->count();
                                    $pct = $total > 0 ? round(($correct / $total) * 100) : 0;
                                @endphp
                                <span class="text-xs font-medium {{ $pct >= 70 ? 'text-emerald-400' : ($pct >= 40 ? 'text-amber-400' : 'text-red-400') }}">
                                    {{ $correct }}/{{ $total }} ({{ $pct }}%)
                                </span>
                            @else
                                <span class="text-xs text-gray-600">—</span>
                            @endif
                        </td>
                        <td class="py-2 px-4 text-right">
                            <div class="inline-flex items-center gap-1">
                                <button wire:click="viewSession({{ $session->id }})"
                                    title="Ver detalles"
                                    class="inline-flex items-center justify-center w-7 h-7 rounded-lg text-xs font-bold bg-purple-500/10 text-purple-400 hover:bg-purple-500/20 border border-purple-500/20 transition-all duration-200">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                    </svg>
                                </button>
                                @if($session->completado_at)
                                    <button wire:click="openAiReport({{ $session->estudiant_id }}, {{ $session->diag_main_id }})"
                                        title="Reporte IA"
                                        class="inline-flex items-center justify-center w-7 h-7 rounded-lg text-xs font-bold bg-cyan-500/10 text-cyan-400 hover:bg-cyan-500/20 border border-cyan-500/20 transition-all duration-200">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                        </svg>
                                    </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="py-12 text-center">
                            <svg class="w-12 h-12 text-gray-700 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                            <p class="text-sm font-medium text-gray-400">No hay sesiones registradas</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    @if($sessions->hasPages())
        <div class="mt-4 pt-4 border-t border-white/5">
            {{ $sessions->links('vendor.livewire.custom-tailwind') }}
        </div>
    @endif
</div>
