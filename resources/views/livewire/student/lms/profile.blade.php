<div class="max-w-4xl mx-auto py-8 px-4 space-y-6">
    <h1 class="text-lg font-bold text-gray-900 dark:text-white">Mi Perfil</h1>

    @if($profileData && $profileData['estudiant'])
        {{-- Datos personales --}}
        <div class="bg-white dark:bg-gray-800/50 border border-gray-200 dark:border-gray-700 rounded-lg p-6 space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="text-[10px] font-bold uppercase tracking-widest text-gray-500">Nombre</label>
                    <p class="text-sm font-medium text-gray-900 dark:text-white">
                        {{ $profileData['estudiant']->full_name ?? $profileData['estudiant']->name . ' ' . $profileData['estudiant']->lastname }}
                    </p>
                </div>
                <div>
                    <label class="text-[10px] font-bold uppercase tracking-widest text-gray-500">Cédula</label>
                    <p class="text-sm font-medium text-gray-900 dark:text-white">
                        {{ $profileData['estudiant']->ci_estudiant ?? $profileData['estudiant']->ci_estudiant_temp ?? '—' }}
                    </p>
                </div>
                <div>
                    <label class="text-[10px] font-bold uppercase tracking-widest text-gray-500">Género</label>
                    <p class="text-sm text-gray-700 dark:text-gray-300">{{ $profileData['estudiant']->gender_sm ?? '—' }}</p>
                </div>
                <div>
                    <label class="text-[10px] font-bold uppercase tracking-widest text-gray-500">Fecha de Nacimiento</label>
                    <p class="text-sm text-gray-700 dark:text-gray-300">
                        @if($profileData['estudiant']->day_birth && $profileData['estudiant']->month_birth && $profileData['estudiant']->year_birth)
                            {{ $profileData['estudiant']->day_birth }}/{{ $profileData['estudiant']->month_birth }}/{{ $profileData['estudiant']->year_birth }}
                        @else
                            {{ $profileData['estudiant']->date_birth && $profileData['estudiant']->date_birth !== '0000-00-00' ? \Carbon\Carbon::parse($profileData['estudiant']->date_birth)->format('d/m/Y') : '—' }}
                        @endif
                    </p>
                </div>
            </div>
        </div>

        {{-- Datos institucionales --}}
        @if($profileData['seccion'])
        <div class="bg-white dark:bg-gray-800/50 border border-gray-200 dark:border-gray-700 rounded-lg p-6 space-y-4">
            <h2 class="text-sm font-bold text-gray-900 dark:text-white">Información Institucional</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="text-[10px] font-bold uppercase tracking-widest text-gray-500">Grado</label>
                    <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $profileData['grado']?->name ?? '—' }}</p>
                </div>
                <div>
                    <label class="text-[10px] font-bold uppercase tracking-widest text-gray-500">Sección</label>
                    <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $profileData['seccion']->name ?? '—' }}</p>
                </div>
                <div>
                    <label class="text-[10px] font-bold uppercase tracking-widest text-gray-500">Plan de Estudio</label>
                    <p class="text-sm text-gray-700 dark:text-gray-300">{{ $profileData['pestudio']?->name ?? '—' }}</p>
                </div>
                <div>
                    <label class="text-[10px] font-bold uppercase tracking-widest text-gray-500">Programa Educativo</label>
                    <p class="text-sm text-gray-700 dark:text-gray-300">{{ $profileData['peducativo']?->name ?? '—' }}</p>
                </div>
            </div>
        </div>
        @endif
    @else
        <div class="text-center py-12">
            <p class="text-gray-500">No se encontraron datos del estudiante.</p>
            <p class="text-xs text-gray-400 mt-1">Contacta al departamento de control de estudio.</p>
        </div>
    @endif
</div>
