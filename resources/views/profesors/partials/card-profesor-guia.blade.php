<div class="mt-8">
    <h2 class="text-lg font-bold text-white mb-6 flex items-center">
        <svg class="w-5 h-5 mr-2 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"></path>
        </svg>
        Secciones como Tutor
    </h2>

    <div class="bg-gray-900/40 backdrop-blur-md border border-white/5 rounded-2xl overflow-hidden">
        <div class="p-5">
            @if($seccionesGuia->isNotEmpty())
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b border-white/5">
                                <th class="text-left px-4 py-3 text-[10px] font-bold uppercase tracking-widest text-gray-500">Grado</th>
                                <th class="text-left px-4 py-3 text-[10px] font-bold uppercase tracking-widest text-gray-500">Sección</th>
                                <th class="text-center px-4 py-3 text-[10px] font-bold uppercase tracking-widest text-gray-500">Informes de Diagnóstico</th>
                                <th class="text-right px-4 py-3 text-[10px] font-bold uppercase tracking-widest text-gray-500">Acción</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-white/5">
                            @foreach($seccionesGuia as $seccion)
                                @php
                                    $gradoName = $seccion->grado->name ?? '?';
                                    $seccionName = $seccion->name ?? '?';
                                    $reportesCount = 0;
                                    if (class_exists(\App\Models\app\Instrument\SectionDiagnosticReport::class)) {
                                        $reportesCount = \App\Models\app\Instrument\SectionDiagnosticReport::where('section_id', $seccion->id)->count();
                                    }
                                @endphp
                                <tr class="hover:bg-white/[0.02] transition-colors group">
                                    <td class="px-4 py-3">
                                        <span class="text-sm text-white font-medium">{{ $gradoName }}</span>
                                    </td>
                                    <td class="px-4 py-3">
                                        <span class="text-sm text-gray-300">Sección {{ $seccionName }}</span>
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        @if($reportesCount > 0)
                                            <span class="inline-flex items-center gap-1 text-[10px] font-bold uppercase tracking-wider text-emerald-400 bg-emerald-500/10 px-2.5 py-1 rounded-full">
                                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span>
                                                {{ $reportesCount }} {{ $reportesCount === 1 ? 'informe' : 'informes' }}
                                            </span>
                                        @else
                                            <span class="text-xs text-gray-500">Sin informes</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-right">
                                        @if($reportesCount > 0)
                                            <div class="flex items-center justify-end gap-1.5 opacity-0 group-hover:opacity-100 transition-opacity">
                                                <a href="#"
                                                    class="p-2 bg-white/5 hover:bg-cyan-500/10 rounded-lg border border-white/5 hover:border-cyan-500/20 text-gray-400 hover:text-cyan-400 transition-all duration-200">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                                    </svg>
                                                </a>
                                            </div>
                                        @else
                                            <span class="text-xs text-gray-600">—</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-8">
                    <svg class="w-12 h-12 text-gray-700 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                    <p class="text-gray-500 font-medium mb-1">Sin secciones asignadas</p>
                    <p class="text-gray-600 text-sm">No tienes tutoría de ninguna sección actualmente.</p>
                </div>
            @endif
        </div>

        {{-- Footer link to reports (only if reports exist) --}}
        @if(isset($tieneReportesDiagnosticos) && $tieneReportesDiagnosticos)
        <div class="border-t border-white/5 px-5 py-4 flex justify-end">
            <a href="#"
                class="inline-flex items-center gap-2 px-4 py-2 bg-emerald-500/10 hover:bg-emerald-500/20 text-emerald-400 rounded-xl border border-emerald-500/20 transition-all duration-300 text-xs font-bold uppercase tracking-widest group/btn">
                <svg class="w-4 h-4 transition-transform group-hover/btn:rotate-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37a1.724 1.724 0 002.572-1.065z"></path>
                </svg>
                Ir a Informes de Diagnóstico
            </a>
        </div>
        @endif
    </div>
</div>
