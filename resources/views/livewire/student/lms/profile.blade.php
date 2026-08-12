<div class="max-w-4xl mx-auto py-8 px-4 space-y-6">
    @if($profileData && $profileData['estudiant'])
        @php $e = $profileData['estudiant']; @endphp

        @php
            // Compute initials from full name (first letters of each word)
            $initials = null;
            if (!empty($e['full_name'] ?? '')) {
                $name = trim($e['full_name']);
                $parts = preg_split('/\s+/', $name);
                $initials = '';
                foreach (array_slice($parts, 0, 2) as $part) {
                    if ($part !== '') {
                        $initials .= strtoupper($part[0]);
                    }
                }
            }
            // Mascot visibility logic (same as in Profile.php mount)
            $showMascot = $e['age'] === null || $e['age'] === '-' || (int) $e['age'] <= 12;
            $mascotEmphasis = $e['age'] !== null && $e['age'] !== '-' && (int) $e['age'] <= 8;
        @endphp

        <div class="flex items-center gap-4">
            <div>
                <x-ui.avatar
                    :initials="$initials"
                    :showMascot="$showMascot"
                    :mascotEmphasis="$mascotEmphasis"
                    class="w-14 h-14"
                />
            </div>
            <div>
                <h1 class="text-lg font-display font-bold text-gray-900 dark:text-white">Mi Perfil</h1>
                <p class="text-xs text-gray-600 dark:text-gray-400">Información personal y académica</p>
            </div>
        </div>

        <x-ui.divider class="my-6"/>

        {{-- ════════════════════ Stats rápidas ════════════════════
             Misma semántica y markup que StudentHome (dashboard canónico):
             conteos reales + progreso real en %. Antes se mostraban los
             conteos crudos como si fueran porcentajes (2%, 0%). --}}
        @if($stats)
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 sm:gap-4">
            {{-- Lecciones --}}
            <div class="bg-white dark:bg-gray-800/50 border border-gray-200 dark:border-gray-700 rounded-xl p-4 space-y-2 shadow-sm transition-all duration-200 ease-out">
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 rounded-lg bg-sky-500/10 flex items-center justify-center">
                        <svg class="w-4 h-4 text-sky-400" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                        </svg>
                    </div>
                    <span class="text-xs font-semibold uppercase tracking-wider text-gray-400 dark:text-gray-500">Lecciones</span>
                </div>
                <p class="text-2xl font-bold text-gray-900 dark:text-white tabular-nums">{{ $stats['total'] }}</p>
                <p class="text-xs text-gray-600 dark:text-gray-400">Disponibles para ti</p>
            </div>

            {{-- Completadas --}}
            <div class="bg-white dark:bg-gray-800/50 border border-gray-200 dark:border-gray-700 rounded-xl p-4 space-y-2 shadow-sm transition-all duration-200 ease-out">
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 rounded-lg bg-emerald-500/10 flex items-center justify-center">
                        <svg class="w-4 h-4 text-emerald-400" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <span class="text-xs font-semibold uppercase tracking-wider text-gray-400 dark:text-gray-500">Completadas</span>
                </div>
                <p class="text-2xl font-bold text-gray-900 dark:text-white tabular-nums">{{ $stats['completed'] }}</p>
                <p class="text-xs text-gray-600 dark:text-gray-400">
                    @if($stats['total'] > 0)
                        {{ $stats['progress_pct'] }}% del total
                    @else
                        Sin actividades
                    @endif
                </p>
            </div>

            {{-- Comentarios --}}
            <div class="bg-white dark:bg-gray-800/50 border border-gray-200 dark:border-gray-700 rounded-xl p-4 space-y-2 shadow-sm transition-all duration-200 ease-out">
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 rounded-lg bg-amber-500/10 flex items-center justify-center">
                        <svg class="w-4 h-4 text-amber-400" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/>
                        </svg>
                    </div>
                    <span class="text-xs font-semibold uppercase tracking-wider text-gray-400 dark:text-gray-500">Comentarios</span>
                </div>
                <p class="text-2xl font-bold text-gray-900 dark:text-white tabular-nums">{{ $stats['comments'] }}</p>
                <p class="text-xs text-gray-600 dark:text-gray-400">Que has dejado</p>
            </div>

            {{-- Descargas --}}
            <div class="bg-white dark:bg-gray-800/50 border border-gray-200 dark:border-gray-700 rounded-xl p-4 space-y-2 shadow-sm transition-all duration-200 ease-out">
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 rounded-lg bg-purple-500/10 flex items-center justify-center">
                        <svg class="w-4 h-4 text-purple-400" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                        </svg>
                    </div>
                    <span class="text-xs font-semibold uppercase tracking-wider text-gray-400 dark:text-gray-500">Descargas</span>
                </div>
                <p class="text-2xl font-bold text-gray-900 dark:text-white tabular-nums">{{ $stats['downloads'] }}</p>
                <p class="text-xs text-gray-600 dark:text-gray-400">Recursos descargados</p>
            </div>
        </div>
        @endif

        <x-ui.divider class="my-6"/>

        {{-- ════════════════════ Datos personales ════════════════════ --}}
        <div class="bg-white dark:bg-gray-800/50 border border-gray-200 dark:border-gray-700 rounded-lg divide-y divide-gray-100 dark:divide-gray-700/50">
            <div class="px-6 py-3">
                <h2 class="text-xs font-bold uppercase tracking-widest text-gray-500">Datos Personales</h2>
            </div>
            <div class="px-6 py-4 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                <x-ui.info-card
                    label="Nombre Completo"
                    :value="$e->full_name"
                    icon="<svg class='w-4 h-4 text-emerald-400' aria-hidden='true'><path fill='currentColor' d='M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25 1.18-6.88-5-4.87L6.91 8.26 10 2z'/></svg>"
                />
                <x-ui.info-card
                    label="Cédula"
                    :value="$e->nacionalidad . '-' . ($e->ci_estudiant ?? $e->ci_estudiant_temp ?? '—')"
                    icon="<svg class='w-4 h-4 text-sky-400' aria-hidden='true'><path fill='currentColor' d='M9 4a2 2 0 012-2h2a2 2 0 012 2v12a2 2 0 01-2 2h-2a2 2 0 01-2-2zm0 6a2 2 0 002 2h2a2 2 0 002-2v-2a2 2 0 00-2-2h-2a2 2 0 00-2-2z'/></svg>"
                />
                <x-ui.info-card
                    label="Género"
                    :value="$e->gender_sm ?? '—'"
                    icon="<svg class='w-4 h-4 text-amber-400' aria-hidden='true'><path fill='currentColor' d='M10 9a3 3 0 100-6 3 3 0 000 6zm0 7a3 3 0 1000-6 3 3 0 000 6z'/></svg>"
                />
                <x-ui.info-card
                    label="Fecha de Nacimiento"
                    :value="($e->day_birth && $e->month_birth && $e->year_birth) ? $e->day_birth . '/' . $e->month_birth . '/' . $e->year_birth : ($e->date_birth && $e->date_birth !== '0000-00-00' ? \Carbon\Carbon::parse($e->date_birth)->format('d/m/Y') : '—')"
                    icon="<svg class='w-4 h-4 text-purple-400' aria-hidden='true'><path fill='currentColor' d='M12 2a10 10 0 00-10 10c0 3.54 1.45 6.66 3.83 8.94l5.59-5.59a1 1 0 011.42 0l5.59 5.59A9.91 9.91 0 0022 12c0-5.52-4.48-10-10-10zM12 16a4 4 0 110-8 4 4 0 010 8z'/></svg>"
                />
                <x-ui.info-card
                    label="Edad"
                    :value="$e->age"
                    icon="<svg class='w-4 h-4 text-gray-400' aria-hidden='true'><path fill='currentColor' d='M12 2c1.1 0 2 .9 2 2s-.9 2-2 2-2-.9-2 2-2 .9-2 2zm0 6a3 3 0 1000-6 3 3 0 000 6z'/></svg>"
                />
                <x-ui.info-card
                    label="Nacionalidad"
                    :value="$e->nacionalidad === 'V' ? 'Venezolana' : 'Extranjera'"
                    icon="<svg class='w-4 h-4 text-gray-400' aria-hidden='true'><path fill='currentColor' d='M12 2.252a9 9 0 100 0 16.506 9 9 0 000-16.506zm0 14.25a3 3 0 100-5.196 3 3 0 0010.392-6 3 3 0 00-10.392 6z'/></svg>"
                />
            </div>
        </div>

        <x-ui.divider class="my-6"/>

        <!-- ══════════════ Lugar de nacimiento ══════════════ -->
        @if($e->city_birth || $e->state_birth || $e->country_birth)
        <div class="bg-white dark:bg-gray-800/50 border border-gray-200 dark:border-gray-700 rounded-lg divide-y divide-gray-100 dark:divide-gray-700/50">
            <div class="px-6 py-3">
                <h2 class="text-xs font-bold uppercase tracking-widest text-gray-500">Lugar de Nacimiento</h2>
            </div>
            <div class="px-6 py-4 grid grid-cols-1 sm:grid-cols-3 gap-4">
                <x-ui.info-card
                    label="Ciudad"
                    :value="$e->city_birth ?? '—'"
                    icon="<svg class='w-4 h-4 text-sky-400' aria-hidden='true'><path fill='currentColor' d='M11 18h-.01M11 11h.01M11 4h.01M8 21v-2a4 4 0 014-4h4a4 4 0 014 4v2'/></svg>"
                />
                <x-ui.info-card
                    label="Estado"
                    :value="$e->state_birth ?? '—'"
                    icon="<svg class='w-4 h-4 text-amber-400' aria-hidden='true'><path fill='currentColor' d='M5 13l4 4L19 7'/></svg>"
                />
                <x-ui.info-card
                    label="País"
                    :value="$e->country_birth ?? '—'"
                    icon="<svg class='w-4 h-4 text-purple-400' aria-hidden='true'><path fill='currentColor' d='M12 2.252a9 9 0 100 0 16.506 9 9 0 000-16.506zm0 14.25a3 3 0 100-5.196 3 3 0 0010.392-6 3 3 0 00-10.392 6z'/></svg>"
                />
            </div>
        </div>
        @endif

        <x-ui.divider class="my-6"/>

        <!-- ══════════════ Contacto ══════════════ -->
        @if($e->email || $e->gsemail || $e->cellphone || $e->phone || $e->dir_address)
        <div class="bg-white dark:bg-gray-800/50 border border-gray-200 dark:border-gray-700 rounded-lg divide-y divide-gray-100 dark:divide-gray-700/50">
            <div class="px-6 py-3">
                <h2 class="text-xs font-bold uppercase tracking-widest text-gray-500">Contacto</h2>
            </div>
            <div class="px-6 py-4 grid grid-cols-1 sm:grid-cols-2 gap-4">
                <x-ui.info-card
                    label="Correo Electrónico"
                    :value="$e->email ?? '—'"
                    icon="<svg class='w-4 h-4 text-sky-400' aria-hidden='true'><path fill='currentColor' d='M4 4h16c1.1 0 2 .9 2 2v8c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z'/></svg>"
                />
                <x-ui.info-card
                    label="Correo Clases Virtuales"
                    :value="$e->gsemail ?? '—'"
                    icon="<svg class='w-4 h-4 text-emerald-400' aria-hidden='true'><path fill='currentColor' d='M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2-2v12a2 2 0 002 2z'/></svg>"
                />
                <x-ui.info-card
                    label="Celular"
                    :value="$e->cellphone ?? '—'"
                    icon="<svg class='w-4 h-4 text-amber-400' aria-hidden='true'><path fill='currentColor' d='M20 13V8a2 2 0 0,1-2-2h-3l-3-3-2-2-2-2-1-1 1 1 2 2 3 3h3a2 2 0 0,1-2-2v5l-3 3-3-3-2-2z'/></svg>"
                />
                <x-ui.info-card
                    label="Teléfono"
                    :value="$e->phone ?? '—'"
                    icon="<svg class='w-4 h-4 text-gray-400' aria-hidden='true'><path fill='currentColor' d='M20 13V8a2 2 0 0,1-2-2h-3l-3-3-2-2-2-2-1-1 1 1 2 2 3 3h3a2 2 0 0,1-2-2v5l-3 3-3-3-2-2z'/></svg>"
                />
                @if($e->dir_address)
                <div class="sm:col-span-2">
                    <x-ui.info-card
                        label="Dirección de Residencia"
                        :value="$e->dir_address"
                        icon="<svg class='w-4 h-4 text-purple-400' aria-hidden='true'><path fill='currentColor' d='M3 9l9-7 9 7m0 0V5a2 2 0 012-2h.5a2 2 0 011.606 .793l1.293 3.707A2 2 0 019.5 15H11a2 2 0 01-1.414-.586l-.707-2.414A2 2 0 018 9v-4z'/></svg>"
                    />
                </div>
                @endif
            </div>
        </div>
        @endif

        <x-ui.divider class="my-6"/>

        @if($e->representant)
        <div class="bg-white dark:bg-gray-800/50 border border-gray-200 dark:border-gray-700 rounded-lg divide-y divide-gray-100 dark:divide-gray-700/50">
            <div class="px-6 py-3">
                <h2 class="text-xs font-bold uppercase tracking-widest text-gray-500">Representante</h2>
            </div>
            <div class="px-6 py-4 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <x-ui.info-card
                    label="Nombre"
                    :value="$e->representant->name_full ?? $e->representant->name ?? '—'"
                    icon="<svg class='w-4 h-4 text-sky-400' aria-hidden='true'><path fill='currentColor' d='M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25 1.18-6.88L12 17.77l-6.18 3.25 1.18-6.88-5-4.87L6.91 8.26 10 2z'/></svg>"
                />
                <x-ui.info-card
                    label="Cédula"
                    :value="$e->representant->ci_representant ?? '—'"
                    icon="<svg class='w-4 h-4 text-amber-400' aria-hidden='true'><path fill='currentColor' d='M9 4a2 2 0 012-2h2a2 2 0 012 2v12a2 2 0 01-2 2h-2a2 2 0 01-2-2zm0 6a2 2 0 002 2h2a2 2 0 002-2v-2a2 2 0 002-2h-2a2 2 0 002-2z'/></svg>"
                />
                <x-ui.info-card
                    label="Celular"
                    :value="$e->representant->cellphone ?? '—'"
                    icon="<svg class='w-4 h-4 text-purple-400' aria-hidden='true'><path fill='currentColor' d='M20 13V8a2 2 0 0,1-2-2h-3l-3-3-2-2-2-2-1-1 1 1 2 2 3 3h3a2 2 0 0,1-2-2v5l-3 3-3-3-2-2z'/></svg>"
                />
                <x-ui.info-card
                    label="Correo"
                    :value="$e->representant->email ?? '—'"
                    icon="<svg class='w-4 h-4 text-gray-400' aria-hidden='true'><path fill='currentColor' d='M4 4h16c1.1 0 2 .9 2 2v8c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z'/></svg>"
                />
            </div>
        </div>
        @endif

        <x-ui.divider class="my-6"/>

        @if($profileData['seccion'])
        <div class="bg-white dark:bg-gray-800/50 border border-gray-200 dark:border-gray-700 rounded-lg divide-y divide-gray-100 dark:divide-gray-700/50">
            <div class="px-6 py-3">
                <h2 class="text-xs font-bold uppercase tracking-widest text-gray-500">Información Institucional</h2>
            </div>
            <div class="px-6 py-4 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <x-ui.info-card
                    label="Grado"
                    :value="$profileData['grado']?->name ?? '—'"
                    icon="<svg class='w-4 h-4 text-sky-400' aria-hidden='true'><path fill='currentColor' d='M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z'/></svg>"
                />
                <x-ui.info-card
                    label="Sección"
                    :value="$profileData['seccion']->name ?? '—'"
                    icon="<svg class='w-4 h-4 text-emerald-400' aria-hidden='true'><path fill='currentColor' d='M12 2.252a9 9 0 100 0 16.506 9 9 0 000-16.506zm0 14.25a3 3 0 100-5.196 3 3 0 0010.392-6 3 3 0 00-10.392 6z'/></svg>"
                />
                <x-ui.info-card
                    label="Plan de Estudio"
                    :value="$profileData['pestudio']?->name ?? '—'"
                    icon="<svg class='w-4 h-4 text-amber-400' aria-hidden='true'><path fill='currentColor' d='M12 2.252a9 9 0 100 0 16.506 9 9 0 000-16.506zm0 14.25a3 3 0 100-5.196 3 3 0 0010.392-6 3 3 0 00-10.392 6z'/></svg>"
                />
                <x-ui.info-card
                    label="Programa Educativo"
                    :value="$profileData['peducativo']?->name ?? '—'"
                    icon="<svg class='w-4 h-4 text-purple-400' aria-hidden='true'><path fill='currentColor' d='M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2-2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z'/></svg>"
                />
            </div>
        </div>
        @endif

        <x-ui.divider class="my-6"/>

        <!-- ══════════════ Enlaces rápidos ══════════════ -->
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
            <a href="{{ route('student.lms.academic') }}"
               class="flex items-center gap-2 px-4 py-3 min-h-[44px] bg-white dark:bg-gray-800/50 border border-gray-200 dark:border-gray-700 rounded-lg hover:border-emerald-500/40 hover:shadow-sm transition-all duration-200 ease-out focus-visible:ring-2 ring-emerald-500/50 focus-visible:ring-offset-2 dark:focus-visible:ring-offset-gray-800">
                <svg class="w-4 h-4 text-sky-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                </svg>
                <span class="text-xs font-medium text-gray-700 dark:text-gray-300">Ver Académica</span>
            </a>
            <a href="{{ route('student.lms.lessons') }}"
               class="flex items-center gap-2 px-4 py-3 min-h-[44px] bg-white dark:bg-gray-800/50 border border-gray-200 dark:border-gray-700 rounded-lg hover:border-emerald-500/40 hover:shadow-sm transition-all duration-200 ease-out focus-visible:ring-2 ring-emerald-500/50 focus-visible:ring-offset-2 dark:focus-visible:ring-offset-gray-800">
                <svg class="w-4 h-4 text-emerald-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                <span class="text-xs font-medium text-gray-700 dark:text-gray-300">Ir a Lecciones</span>
            </a>
            <a href="{{ route('student.lms.resources') }}"
               class="flex items-center gap-2 px-4 py-3 min-h-[44px] bg-white dark:bg-gray-800/50 border border-gray-200 dark:border-gray-700 rounded-lg hover:border-emerald-500/40 hover:shadow-sm transition-all duration-200 ease-out focus-visible:ring-2 ring-emerald-500/50 focus-visible:ring-offset-2 dark:focus-visible:ring-offset-gray-800">
                <svg class="w-4 h-4 text-amber-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                </svg>
                <span class="text-xs font-medium text-gray-700 dark:text-gray-300">Ver Recursos</span>
            </a>
            <a href="{{ route('student.lms.home') }}"
               class="flex items-center gap-2 px-4 py-3 min-h-[44px] bg-white dark:bg-gray-800/50 border border-gray-200 dark:border-gray-700 rounded-lg hover:border-emerald-500/40 hover:shadow-sm transition-all duration-200 ease-out focus-visible:ring-2 ring-emerald-500/50 focus-visible:ring-offset-2 dark:focus-visible:ring-offset-gray-800">
                <svg class="w-4 h-4 text-purple-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 001 1h2a1 1 0 001 1v4a1 1 0 001 1m-6 0h6"/>
                </svg>
                <span class="text-xs font-medium text-gray-700 dark:text-gray-300">Ir a Inicio</span>
            </a>
        </div>
    @else
        <div class="text-center py-16">
            @php
                // For the empty state, we still need to compute mascot visibility
                $authUser = auth()->user();
                $age = $authUser?->estudiant?->age;
                $showMascot = $age === null || $age === '-' || (int) $age <= 12;
                $mascotEmphasis = $age !== null && $age !== '-' && (int) $age <= 8;
            @endphp
            @if($showMascot)
                <x-lms.mascot :variant="'idle'" :size="'sm'" :emphasis="$mascotEmphasis" />
            @else
                <div class="w-16 h-16 rounded-2xl bg-gray-100 dark:bg-gray-800 flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                </div>
            @endif
            <p class="text-gray-600 font-medium">No se encontraron datos del estudiante</p>
            <p class="text-xs text-gray-500 mt-1">Verifica que tu usuario esté vinculado a un estudiante activo.</p>
            <p class="text-xs text-gray-500 mt-3">Si el problema persiste, contacta al departamento de control de estudio.</p>
        </div>
    @endif
</div>