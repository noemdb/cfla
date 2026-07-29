<div class="max-w-4xl mx-auto py-8 px-4 space-y-6">
    <div class="flex items-center gap-4">
        <div class="w-12 h-12 rounded-xl bg-emerald-500/10 flex items-center justify-center shrink-0">
            <svg class="w-6 h-6 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
            </svg>
        </div>
        <div>
            <h1 class="text-lg font-bold text-gray-900 dark:text-white">Mi Perfil</h1>
            <p class="text-xs text-gray-500 dark:text-gray-400">Información personal y académica</p>
        </div>
    </div>

    @if($profileData && $profileData['estudiant'])
        @php $e = $profileData['estudiant']; @endphp

        {{-- ═══ Stats rápidas ═══ --}}
        @if($stats)
        <div class="grid grid-cols-3 gap-3">
            <div class="bg-white dark:bg-gray-800/50 border border-gray-200 dark:border-gray-700 rounded-lg p-4 text-center">
                <p class="text-lg font-bold text-emerald-400">{{ $stats['total_activities'] }}</p>
                <p class="text-[10px] font-medium text-gray-500 dark:text-gray-400 mt-0.5">Actividades</p>
            </div>
            <div class="bg-white dark:bg-gray-800/50 border border-gray-200 dark:border-gray-700 rounded-lg p-4 text-center">
                <p class="text-lg font-bold text-sky-400">{{ $stats['total_lessons'] }}</p>
                <p class="text-[10px] font-medium text-gray-500 dark:text-gray-400 mt-0.5">Lecciones</p>
            </div>
            <div class="bg-white dark:bg-gray-800/50 border border-gray-200 dark:border-gray-700 rounded-lg p-4 text-center">
                <p class="text-lg font-bold text-amber-400">{{ $stats['total_comments'] }}</p>
                <p class="text-[10px] font-medium text-gray-500 dark:text-gray-400 mt-0.5">Comentarios</p>
            </div>
        </div>
        @endif

        {{-- ═══ Datos personales ═══ --}}
        <div class="bg-white dark:bg-gray-800/50 border border-gray-200 dark:border-gray-700 rounded-lg divide-y divide-gray-100 dark:divide-gray-700/50">
            <div class="px-6 py-3">
                <h2 class="text-xs font-bold uppercase tracking-widest text-gray-500">Datos Personales</h2>
            </div>
            <div class="px-6 py-4 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                <div>
                    <label class="text-[10px] font-bold uppercase tracking-widest text-gray-500">Nombre Completo</label>
                    <p class="text-sm font-medium text-gray-900 dark:text-white mt-0.5">
                        {{ $e->full_name }}
                    </p>
                </div>
                <div>
                    <label class="text-[10px] font-bold uppercase tracking-widest text-gray-500">Cédula</label>
                    <p class="text-sm font-medium text-gray-900 dark:text-white mt-0.5">
                        {{ $e->nacionalidad }}-{{ $e->ci_estudiant ?? $e->ci_estudiant_temp ?? '—' }}
                    </p>
                </div>
                <div>
                    <label class="text-[10px] font-bold uppercase tracking-widest text-gray-500">Género</label>
                    <p class="text-sm text-gray-700 dark:text-gray-300 mt-0.5">{{ $e->gender_sm ?? '—' }}</p>
                </div>
                <div>
                    <label class="text-[10px] font-bold uppercase tracking-widest text-gray-500">Fecha de Nacimiento</label>
                    <p class="text-sm text-gray-700 dark:text-gray-300 mt-0.5">
                        @if($e->day_birth && $e->month_birth && $e->year_birth)
                            {{ $e->day_birth }}/{{ $e->month_birth }}/{{ $e->year_birth }}
                        @else
                            {{ $e->date_birth && $e->date_birth !== '0000-00-00' ? \Carbon\Carbon::parse($e->date_birth)->format('d/m/Y') : '—' }}
                        @endif
                    </p>
                </div>
                <div>
                    <label class="text-[10px] font-bold uppercase tracking-widest text-gray-500">Edad</label>
                    <p class="text-sm text-gray-700 dark:text-gray-300 mt-0.5">{{ $e->age }}</p>
                </div>
                <div>
                    <label class="text-[10px] font-bold uppercase tracking-widest text-gray-500">Nacionalidad</label>
                    <p class="text-sm text-gray-700 dark:text-gray-300 mt-0.5">{{ $e->nacionalidad === 'V' ? 'Venezolana' : 'Extranjera' }}</p>
                </div>
            </div>
        </div>

        <!-- ═══ Lugar de nacimiento ═══ -->
        @if($e->city_birth || $e->state_birth || $e->country_birth)
        <div class="bg-white dark:bg-gray-800/50 border border-gray-200 dark:border-gray-700 rounded-lg divide-y divide-gray-100 dark:divide-gray-700/50">
            <div class="px-6 py-3">
                <h2 class="text-xs font-bold uppercase tracking-widest text-gray-500">Lugar de Nacimiento</h2>
            </div>
            <div class="px-6 py-4 grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label class="text-[10px] font-bold uppercase tracking-widest text-gray-500">Ciudad</label>
                    <p class="text-sm text-gray-700 dark:text-gray-300 mt-0.5">{{ $e->city_birth ?? '—' }}</p>
                </div>
                <div>
                    <label class="text-[10px] font-bold uppercase tracking-widest text-gray-500">Estado</label>
                    <p class="text-sm text-gray-700 dark:text-gray-300 mt-0.5">{{ $e->state_birth ?? '—' }}</p>
                </div>
                <div>
                    <label class="text-[10px] font-bold uppercase tracking-widest text-gray-500">País</label>
                    <p class="text-sm text-gray-700 dark:text-gray-300 mt-0.5">{{ $e->country_birth ?? '—' }}</p>
                </div>
            </div>
        </div>
        @endif

        <!-- ═══ Contacto ═══ -->
        @if($e->email || $e->gsemail || $e->cellphone || $e->phone || $e->dir_address)
        <div class="bg-white dark:bg-gray-800/50 border border-gray-200 dark:border-gray-700 rounded-lg divide-y divide-gray-100 dark:divide-gray-700/50">
            <div class="px-6 py-3">
                <h2 class="text-xs font-bold uppercase tracking-widest text-gray-500">Contacto</h2>
            </div>
            <div class="px-6 py-4 grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="text-[10px] font-bold uppercase tracking-widest text-gray-500">Correo Electrónico</label>
                    <p class="text-sm text-gray-700 dark:text-gray-300 mt-0.5">{{ $e->email ?? '—' }}</p>
                </div>
                <div>
                    <label class="text-[10px] font-bold uppercase tracking-widest text-gray-500">Correo Clases Virtuales</label>
                    <p class="text-sm text-gray-700 dark:text-gray-300 mt-0.5">{{ $e->gsemail ?? '—' }}</p>
                </div>
                <div>
                    <label class="text-[10px] font-bold uppercase tracking-widest text-gray-500">Celular</label>
                    <p class="text-sm text-gray-700 dark:text-gray-300 mt-0.5">{{ $e->cellphone ?? '—' }}</p>
                </div>
                <div>
                    <label class="text-[10px] font-bold uppercase tracking-widest text-gray-500">Teléfono</label>
                    <p class="text-sm text-gray-700 dark:text-gray-300 mt-0.5">{{ $e->phone ?? '—' }}</p>
                </div>
                @if($e->dir_address)
                <div class="sm:col-span-2">
                    <label class="text-[10px] font-bold uppercase tracking-widest text-gray-500">Dirección de Residencia</label>
                    <p class="text-sm text-gray-700 dark:text-gray-300 mt-0.5">{{ $e->dir_address }}</p>
                </div>
                @endif
            </div>
        </div>
        @endif

        @if($e->representant)
        <div class="bg-white dark:bg-gray-800/50 border border-gray-200 dark:border-gray-700 rounded-lg divide-y divide-gray-100 dark:divide-gray-700/50">
            <div class="px-6 py-3">
                <h2 class="text-xs font-bold uppercase tracking-widest text-gray-500">Representante</h2>
            </div>
            <div class="px-6 py-4 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <div>
                    <label class="text-[10px] font-bold uppercase tracking-widest text-gray-500">Nombre</label>
                    <p class="text-sm font-medium text-gray-900 dark:text-white mt-0.5">{{ $e->representant->name_full ?? $e->representant->name ?? '—' }}</p>
                </div>
                <div>
                    <label class="text-[10px] font-bold uppercase tracking-widest text-gray-500">Cédula</label>
                    <p class="text-sm text-gray-700 dark:text-gray-300 mt-0.5">{{ $e->representant->ci_representant ?? '—' }}</p>
                </div>
                <div>
                    <label class="text-[10px] font-bold uppercase tracking-widest text-gray-500">Celular</label>
                    <p class="text-sm text-gray-700 dark:text-gray-300 mt-0.5">{{ $e->representant->cellphone ?? '—' }}</p>
                </div>
                <div>
                    <label class="text-[10px] font-bold uppercase tracking-widest text-gray-500">Correo</label>
                    <p class="text-sm text-gray-700 dark:text-gray-300 mt-0.5 truncate">{{ $e->representant->email ?? '—' }}</p>
                </div>
            </div>
        </div>
        @endif

        @if($profileData['seccion'])
        <div class="bg-white dark:bg-gray-800/50 border border-gray-200 dark:border-gray-700 rounded-lg divide-y divide-gray-100 dark:divide-gray-700/50">
            <div class="px-6 py-3">
                <h2 class="text-xs font-bold uppercase tracking-widest text-gray-500">Información Institucional</h2>
            </div>
            <div class="px-6 py-4 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <div>
                    <label class="text-[10px] font-bold uppercase tracking-widest text-gray-500">Grado</label>
                    <p class="text-sm font-medium text-gray-900 dark:text-white mt-0.5">{{ $profileData['grado']?->name ?? '—' }}</p>
                </div>
                <div>
                    <label class="text-[10px] font-bold uppercase tracking-widest text-gray-500">Sección</label>
                    <p class="text-sm font-medium text-gray-900 dark:text-white mt-0.5">{{ $profileData['seccion']->name ?? '—' }}</p>
                </div>
                <div>
                    <label class="text-[10px] font-bold uppercase tracking-widest text-gray-500">Plan de Estudio</label>
                    <p class="text-sm text-gray-700 dark:text-gray-300 mt-0.5">{{ $profileData['pestudio']?->name ?? '—' }}</p>
                </div>
                <div>
                    <label class="text-[10px] font-bold uppercase tracking-widest text-gray-500">Programa Educativo</label>
                    <p class="text-sm text-gray-700 dark:text-gray-300 mt-0.5">{{ $profileData['peducativo']?->name ?? '—' }}</p>
                </div>
            </div>
        </div>
        @endif

        <!-- ═══ Enlaces rápidos ═══ -->
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
            <a href="{{ route('student.lms.academic') }}"
               class="flex items-center gap-2 px-4 py-3 bg-white dark:bg-gray-800/50 border border-gray-200 dark:border-gray-700 rounded-lg hover:border-emerald-500/30 transition-colors">
                <svg class="w-4 h-4 text-sky-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                </svg>
                <span class="text-xs font-medium text-gray-700 dark:text-gray-300">Ver Académica</span>
            </a>
            <a href="{{ route('student.lms.lessons') }}"
               class="flex items-center gap-2 px-4 py-3 bg-white dark:bg-gray-800/50 border border-gray-200 dark:border-gray-700 rounded-lg hover:border-emerald-500/30 transition-colors">
                <svg class="w-4 h-4 text-emerald-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                <span class="text-xs font-medium text-gray-700 dark:text-gray-300">Ir a Lecciones</span>
            </a>
            <a href="{{ route('student.lms.resources') }}"
               class="flex items-center gap-2 px-4 py-3 bg-white dark:bg-gray-800/50 border border-gray-200 dark:border-gray-700 rounded-lg hover:border-emerald-500/30 transition-colors">
                <svg class="w-4 h-4 text-amber-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                </svg>
                <span class="text-xs font-medium text-gray-700 dark:text-gray-300">Ver Recursos</span>
            </a>
            <a href="{{ route('student.lms.home') }}"
               class="flex items-center gap-2 px-4 py-3 bg-white dark:bg-gray-800/50 border border-gray-200 dark:border-gray-700 rounded-lg hover:border-emerald-500/30 transition-colors">
                <svg class="w-4 h-4 text-purple-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                </svg>
                <span class="text-xs font-medium text-gray-700 dark:text-gray-300">Ir a Inicio</span>
            </a>
        </div>
    @else
        <div class="text-center py-16">
            <div class="w-16 h-16 rounded-2xl bg-gray-100 dark:bg-gray-800 flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                </svg>
            </div>
            <p class="text-gray-500 font-medium">No se encontraron datos del estudiante</p>
            <p class="text-xs text-gray-400 mt-1">Verifica que tu usuario esté vinculado a un estudiante activo.</p>
            <p class="text-xs text-gray-400 mt-3">Si el problema persiste, contacta al departamento de control de estudio.</p>
        </div>
    @endif
</div>
