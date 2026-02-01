<div class="bg-gray-900 border border-green-800 rounded-xl shadow-2xl p-6">
    @if($step == 1)
        <!-- Paso 1: Solicitar CI del Representante -->
        <div class="text-center">
            <div class="mb-6">
                <div class="w-20 h-20 mx-auto mb-4 bg-gradient-to-br from-green-700 to-green-900 rounded-full flex items-center justify-center shadow-xl border-2 border-green-600">
                    <x-icon name="identification" class="w-10 h-10 text-green-100" />
                </div>
                <h2 class="text-2xl font-bold text-white mb-2">
                    Identificación del Representante
                </h2>
                <p class="text-gray-300">
                    Ingrese su cédula de identidad para continuar
                </p>
            </div>

            <form wire:submit.prevent="searchRepresentant" class="max-w-md mx-auto">
                <div class="mb-6">
                    <label class="block text-sm font-medium text-green-200 mb-2">Cédula de Identidad</label>
                    <div class="relative">
                        <input
                            wire:model="ci_representant"
                            type="text"
                            placeholder="Ej: 12345678"
                            class="w-full px-4 py-3 text-center text-lg bg-gray-800 border-2 border-green-700 rounded-lg text-white placeholder-gray-400 focus:border-green-500 focus:ring-2 focus:ring-green-500 focus:outline-none transition-all duration-200"
                        />
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <x-icon name="identification" class="w-5 h-5 text-green-400" />
                        </div>
                    </div>
                </div>

                <button
                    type="submit"
                    class="w-full inline-flex items-center justify-center px-6 py-3 bg-gradient-to-r from-green-700 to-green-800 hover:from-green-600 hover:to-green-700 text-white font-medium text-lg rounded-lg shadow-xl transition-all duration-200 transform hover:scale-105 border border-green-600"
                    wire:loading.attr="disabled"
                    wire:target="searchRepresentant"
                >
                    <x-icon name="magnifying-glass" class="w-5 h-5 mr-2" />
                    <span wire:loading.remove wire:target="searchRepresentant">Buscar Representante</span>
                    <span wire:loading wire:target="searchRepresentant">Buscando...</span>
                </button>
            </form>
        </div>

    @elseif($step == 2)
        <!-- Paso 2: Seleccionar Estudiantes -->
        <div>
            <div class="mb-6">
                <div class="flex items-center mb-4">
                    <div class="w-12 h-12 bg-gradient-to-br from-green-700 to-green-900 rounded-full flex items-center justify-center mr-4 border-2 border-green-600">
                        <x-icon name="users" class="w-6 h-6 text-green-100" />
                    </div>
                    <div>
                        <h2 class="text-2xl font-bold text-white">
                            Confirmación de Prosecución
                        </h2>
                        <p class="text-gray-300">
                            Representante: <span class="font-semibold text-green-300">{{ $representant->fullname }}</span>
                        </p>
                    </div>
                </div>
                <p class="text-gray-300 bg-green-900/30 p-3 rounded-lg border-l-4 border-green-600">
                    Seleccione los estudiantes que continuarán en la institución para el período escolar 2024-2025
                </p>

                @if(!empty($confirmedEstudiants))
                    <div class="mt-4 p-3 bg-green-800/20 border border-green-700 rounded-lg">
                        <p class="text-sm text-green-200">
                            <x-icon name="information-circle" class="w-4 h-4 inline mr-1" />
                            Los estudiantes ya confirmados no pueden ser desmarcados
                        </p>
                    </div>
                @endif
            </div>

            <form wire:submit.prevent="confirmProsecucion">
                <div class="space-y-4 mb-6">
                    @foreach($estudiants as $estudiant)
                        @php
                            $isConfirmed = in_array($estudiant['id'], $confirmedEstudiants);
                        @endphp

                        <div class="border-2 rounded-xl p-4 transition-all duration-200 {{ $isConfirmed ? 'bg-gradient-to-r from-green-900/40 to-green-800/40 border-green-600' : 'border-gray-700 hover:border-green-700 bg-gray-800/50' }}">
                            <label class="flex items-center cursor-pointer">
                                <input
                                    type="checkbox"
                                    wire:model="selectedEstudiants"
                                    value="{{ $estudiant['id'] }}"
                                    {{ $isConfirmed ? 'disabled' : '' }}
                                    class="w-5 h-5 text-green-600 bg-gray-700 border-gray-600 rounded focus:ring-green-500 focus:ring-2 {{ $isConfirmed ? 'opacity-75' : '' }}"
                                >
                                <div class="ml-4 flex-1">
                                    <div class="flex justify-between items-start">
                                        <div>
                                            <h3 class="text-lg font-semibold text-white flex items-center">
                                                {{ $estudiant['lastname'] }} {{ $estudiant['name'] }}
                                                @if($isConfirmed)
                                                    <span class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gradient-to-r from-green-800 to-green-900 text-green-200 border border-green-600">
                                                        <x-icon name="check-circle" class="w-3 h-3 mr-1" />
                                                        Confirmado
                                                    </span>
                                                @endif
                                            </h3>
                                            <p class="text-sm text-gray-300">
                                                CI: {{ $estudiant['ci_estudiant'] }}
                                            </p>

                                            <!-- Mostrar información de inscripción -->
                                            @if(isset($estudiant['inscripcion']) && $estudiant['inscripcion'])
                                                @php
                                                    $inscripcion = $estudiant['inscripcion'];
                                                    $seccion = $inscripcion['seccion'] ?? null;
                                                    $grado = $seccion['grado'] ?? null;
                                                @endphp

                                                @if($grado && $seccion)
                                                    <p class="text-sm text-green-300 font-medium">
                                                        <x-icon name="academic-cap" class="w-4 h-4 inline mr-1" />
                                                        {{ $grado['name'] }} {{ $seccion['name'] }}
                                                    </p>
                                                @endif
                                            @endif

                                            @if($isConfirmed)
                                                <p class="text-xs text-green-400 mt-1 font-medium">
                                                    ✓ Este estudiante ya ha sido confirmado para prosecución
                                                </p>
                                            @endif
                                        </div>
                                        <div class="text-right">
                                            <span class="text-sm text-gray-400">
                                                @if($estudiant['date_birth'] && $estudiant['date_birth'] !== '0000-00-00')
                                                    Edad: {{ \Carbon\Carbon::parse($estudiant['date_birth'])->age }} años
                                                @else
                                                    Edad: -
                                                @endif
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </label>
                        </div>
                    @endforeach
                </div>

                <div class="flex justify-between">
                    <button
                        wire:click="resetWizard"
                        type="button"
                        class="inline-flex items-center px-6 py-3 bg-gray-700 hover:bg-gray-600 text-white font-medium text-lg rounded-lg shadow-lg transition-colors duration-200 border border-gray-600"
                    >
                        <x-icon name="arrow-left" class="w-5 h-5 mr-2" />
                        Volver
                    </button>

                    <button
                        type="submit"
                        class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-green-700 to-green-800 hover:from-green-600 hover:to-green-700 text-white font-medium text-lg rounded-lg shadow-xl transition-all duration-200 transform hover:scale-105 border border-green-600"
                        wire:loading.attr="disabled"
                        wire:target="confirmProsecucion"
                    >
                        <x-icon name="check" class="w-5 h-5 mr-2" />
                        <span wire:loading.remove wire:target="confirmProsecucion">Confirmar Prosecución</span>
                        <span wire:loading wire:target="confirmProsecucion">Confirmando...</span>
                    </button>
                </div>
            </form>
        </div>

    @elseif($step == 3)
        <!-- Paso 3: Confirmación y Descarga -->
        <div class="text-center">
            <div class="mb-6">
                <div class="w-20 h-20 mx-auto mb-4 bg-gradient-to-br from-green-600 to-green-800 rounded-full flex items-center justify-center shadow-xl animate-pulse border-2 border-green-500">
                    <x-icon name="check-circle" class="w-10 h-10 text-green-100" />
                </div>
                <h2 class="text-2xl font-bold text-white mb-2">
                    ¡Prosecución Confirmada!
                </h2>
                <p class="text-gray-300">
                    La confirmación de prosecución ha sido registrada exitosamente
                </p>
            </div>

            <div class="bg-gradient-to-r from-green-900/40 to-green-800/40 rounded-xl p-6 mb-6 border border-green-700">
                <h3 class="text-lg font-semibold text-white mb-4 flex items-center justify-center">
                    <x-icon name="users" class="w-5 h-5 mr-2 text-green-300" />
                    Estudiantes Confirmados para Prosecución
                </h3>
                <div class="space-y-2">
                    @foreach($estudiants as $estudiant)
                        @if(in_array($estudiant['id'], $selectedEstudiants))
                            <div class="flex justify-between items-center py-3 px-4 bg-gray-800 rounded-lg border border-green-700 shadow-sm">
                                <span class="text-white font-medium">{{ $estudiant['lastname'] }} {{ $estudiant['name'] }}</span>
                                <div class="text-right">
                                    @php
                                        $inscripcion = $estudiant['inscripcion'] ?? null;
                                        $seccion = $inscripcion['seccion'] ?? null;
                                        $grado = $seccion['grado'] ?? null;
                                        $gradoSeccion = ($grado && $seccion) ? $grado['name'] . ' ' . $seccion['name'] : 'No asignado';
                                    @endphp
                                    <span class="text-sm text-green-300 font-medium">
                                        {{ $gradoSeccion }}
                                    </span>
                                    @if(in_array($estudiant['id'], $confirmedEstudiants))
                                        <span class="ml-2 inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-800 text-green-200 border border-green-600">
                                            Previamente confirmado
                                        </span>
                                    @endif
                                </div>
                            </div>
                        @endif
                    @endforeach
                </div>
            </div>

            <div class="mb-6">
                <h3 class="text-lg font-semibold text-white mb-4 flex items-center justify-center">
                    <x-icon name="qrcode" class="w-5 h-5 mr-2 text-green-300" />
                    Código QR para Descarga de Planilla
                </h3>
                <div class="flex justify-center mb-4">
                    <div class="bg-white p-6 rounded-xl shadow-2xl border-4 border-green-600">
                        @if($qrCode)
                            <img src="{{ $qrCode }}" alt="Código QR" class="w-48 h-48">
                        @endif
                    </div>
                </div>
                <p class="text-sm text-gray-300 mb-4 bg-green-900/30 p-3 rounded-lg border border-green-700">
                    Escanee el código QR o use el botón de descarga para obtener su planilla de prosecución
                </p>
            </div>

            <div class="flex justify-center space-x-4">
                <!-- Enlace directo para descarga -->
                <a
                    href="{{ $downloadUrl }}"
                    target="_blank"
                    class="inline-flex items-center px-8 py-4 bg-gradient-to-r from-green-700 to-green-800 hover:from-green-600 hover:to-green-700 text-white font-medium text-lg rounded-xl shadow-xl transition-all duration-200 transform hover:scale-105 border border-green-600"
                >
                    <svg class="w-6 h-6 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    Descargar Planilla
                </a>

                <button
                    wire:click="resetWizard"
                    class="inline-flex items-center px-6 py-4 bg-gray-700 hover:bg-gray-600 text-white font-medium text-lg rounded-xl shadow-lg transition-colors duration-200 border border-gray-600"
                >
                    <x-icon name="arrow-path" class="w-5 h-5 mr-2" />
                    Nueva Consulta
                </button>
            </div>
        </div>
    @endif
</div>
