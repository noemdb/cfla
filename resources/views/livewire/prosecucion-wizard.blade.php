<div class="flex flex-col min-h-screen bg-black lg:flex-row">

    {{-- Background image overlay --}}
    <div style="background-image: url('{{ asset('image/bg/census.jpg') }}')"
        class="absolute inset-0 z-0 h-full w-full bg-[url('{{ asset('image/bg/census.jpg') }}')] bg-cover bg-center opacity-10">
    </div>

    {{-- Estado de carga (Spinner) --}}
    <div wire:loading wire:target="searchRepresentant, confirmProsecucion"
        class="fixed top-0 left-0 w-full h-full flex items-center justify-center bg-gray-500 bg-opacity-50 z-50">
        <div class="bg-green-200 p-4 rounded-lg shadow-lg flex flex-col items-center">
            <svg class="animate-spin h-8 w-8 text-green-500" xmlns="http://www.w3.org/2000/svg" fill="none"
                viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4">
                </circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
            </svg>
            <p class="mt-2 text-gray-700 font-semibold">Preparando el siguiente paso...</p>
        </div>
    </div>

    {{-- Left Section --}}
    <div class="relative block w-full p-2 sm:p-8 lg:w-1/2 lg:block">
        <div class="w-full z-10">
            @include('livewire.prosecucion.section.left')
        </div>
    </div>

    {{-- Right Section --}}
    <div class="flex w-full items-center sm:justify-center lg:justify-start lg:w-1/2 bg-black p-6">
        <div class="w-full z-10 max-w-md rounded-[40px] p-6 lg:p-12">

            @if($step == 1)
                {{-- ===== PASO 1: CI ===== --}}
                <div class="max-w-sm mx-auto">
                    <h2 class="mb-2 text-lg font-bold text-white">Identificación del Representante</h2>
                    <p class="mb-2 text-gray-400">Ingrese su número de cédula para comenzar</p>

                    <form wire:submit.prevent="searchRepresentant" class="space-y-4">
                        <div class="space-y-2">
                            @error('ci_representant')
                                <div class="p-4 mb-2 text-sm text-red-100 bg-red-900 border border-red-700 rounded-lg shadow-lg" role="alert">
                                    <span class="font-bold">{{ $message }}</span>
                                </div>
                            @enderror
                            <x-input wire:model="ci_representant" label="Cédula de Identidad" placeholder="Ej: 12345678"
                                right-icon="identification" />
                        </div>

                        <x-button wire:click="searchRepresentant" xl positive label="Buscar Representante"
                            class="w-full !bg-green-800 hover:!bg-green-900 border-2 !border-green-900 shadow-lg" type="button" />
                    </form>
                </div>

            @elseif($step == 2)
                {{-- ===== PASO 2: Selección ===== --}}
                <div class="mx-auto w-full max-w-lg">
                    <h2 class="mb-2 text-lg font-bold text-white">Seleccionar Estudiantes</h2>
                    <p class="mb-2 text-gray-400">
                        Representante:
                        <span class="text-gray-300 font-medium">{{ $representant->fullname ?? ($representant->name ?? '') }}</span>
                    </p>

                    @if(!empty($confirmedEstudiants))
                        <div class="p-4 mb-4 text-sm text-emerald-100 bg-green-900/60 border border-green-700 rounded-lg shadow-lg">
                            <div class="flex items-center gap-2">
                                <x-icon name="information-circle" class="w-5 h-5 shrink-0 text-green-300" />
                                <span>Los estudiantes ya confirmados en prosecuciones anteriores aparecen marcados y no pueden desmarcarse.</span>
                            </div>
                        </div>
                    @endif

                    <form wire:submit.prevent="confirmProsecucion">
                        <div class="space-y-2 mb-5 max-h-[55vh] overflow-y-auto pr-1 -mr-1">
                            @foreach($estudiants as $estudiant)
                                @php $isConfirmed = in_array($estudiant['id'], $confirmedEstudiants); @endphp
                                <div class="p-5 bg-[#1a1a1b] border border-[#2d2f33] rounded-[24px] shadow-lg transition-all duration-300 hover:border-green-700 {{ $isConfirmed ? 'border-green-700' : '' }}">
                                    <label class="flex items-start gap-3 cursor-pointer">
                                        <input type="checkbox"
                                               wire:model="selectedEstudiants"
                                               value="{{ $estudiant['id'] }}"
                                               {{ $isConfirmed ? 'disabled checked' : '' }}
                                               class="mt-1 w-5 h-5 rounded border-2 {{ $isConfirmed ? 'border-green-600 bg-green-800' : 'border-gray-600 bg-transparent' }} text-green-600 focus:ring-green-500">

                                        <div class="flex-1 min-w-0">
                                            <div class="flex items-center gap-2">
                                                <span class="text-sm font-medium text-white truncate">{{ $estudiant['lastname'] }} {{ $estudiant['name'] }}</span>
                                                @if($isConfirmed)
                                                    <span class="text-[10px] px-2 py-1 rounded-full bg-green-800 text-green-200 border border-green-700 whitespace-nowrap">Confirmado</span>
                                                @endif
                                            </div>
                                            <div class="mt-1 text-sm text-gray-400 flex flex-wrap gap-x-3">
                                                <span class="font-mono">CI: {{ $estudiant['ci_estudiant'] }}</span>
                                                @php
                                                    $insc = $estudiant['inscripcion'] ?? null;
                                                    $sec = $insc['seccion'] ?? null;
                                                    $grd = $sec['grado'] ?? null;
                                                @endphp
                                                @if($grd && $sec)
                                                    <span>{{ $grd['name'] }} {{ $sec['name'] }}</span>
                                                @endif
                                                @if(($estudiant['date_birth'] ?? null) && $estudiant['date_birth'] !== '0000-00-00')
                                                    <span>{{ \Carbon\Carbon::parse($estudiant['date_birth'])->age }} años</span>
                                                @endif
                                            </div>
                                        </div>
                                    </label>
                                </div>
                            @endforeach
                        </div>

                        <div class="space-y-2">
                            <x-button wire:click="confirmProsecucion" xl positive label="Confirmar Prosecución"
                                class="w-full !bg-green-800 hover:!bg-green-900 border-2 !border-green-900 shadow-lg" />

                            <x-button wire:click="resetWizard" xl flat white label="Volver"
                                class="w-full !border-gray-700 hover:!bg-gray-800" />
                        </div>
                    </form>
                </div>

            @elseif($step == 3)
                {{-- ===== PASO 3: Confirmación ===== --}}
                <div class="mx-auto max-w-sm">
                    <h2 class="mb-2 text-lg font-bold text-white text-center">Prosecución Confirmada</h2>
                    <p class="mb-4 text-gray-400 text-center">Registro completado exitosamente</p>

                    {{-- Confirmed students summary --}}
                    <div class="bg-[#1a1a1b] border border-[#2d2f33] rounded-[24px] p-5 mb-5">
                        <h3 class="text-sm font-semibold text-gray-300 mb-3 flex items-center gap-2">
                            <x-icon name="user-group" class="w-4 h-4" />
                            Estudiantes Confirmados
                        </h3>
                        <div class="space-y-2">
                            @foreach($estudiants as $estudiant)
                                @if(in_array($estudiant['id'], $selectedEstudiants))
                                    @php
                                        $insc = $estudiant['inscripcion'] ?? null;
                                        $sec = $insc['seccion'] ?? null;
                                        $grd = $sec['grado'] ?? null;
                                        $gradoSeccion = ($grd && $sec) ? $grd['name'] . ' ' . $sec['name'] : '';
                                    @endphp
                                    <div class="flex items-center justify-between px-3 py-2 bg-black/40 rounded-lg border border-[#2d2f33]">
                                        <div class="flex items-center gap-2 min-w-0">
                                            <x-icon name="check-circle" class="w-4 h-4 text-green-500 shrink-0" />
                                            <span class="text-sm font-medium text-white truncate">{{ $estudiant['lastname'] }} {{ $estudiant['name'] }}</span>
                                        </div>
                                        <div class="flex items-center gap-2 flex-shrink-0 ml-2">
                                            @if($gradoSeccion)
                                                <span class="text-xs text-green-400/80 font-medium">{{ $gradoSeccion }}</span>
                                            @endif
                                            @if(in_array($estudiant['id'], $confirmedEstudiants))
                                                <span class="text-[10px] px-1.5 py-0.5 rounded-full bg-green-800 text-green-200 border border-green-700">Previo</span>
                                            @endif
                                        </div>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    </div>

                    {{-- QR + Actions --}}
                    <div class="flex items-start gap-4 mb-5">
                        <div class="flex-shrink-0">
                            @if($qrCode)
                                <div class="w-24 h-24 md:w-28 md:h-28 bg-white rounded-xl p-1.5 shadow-lg border border-green-800">
                                    <img src="{{ $qrCode }}" alt="Código QR" class="w-full h-full">
                                </div>
                            @endif
                        </div>
                        <div class="flex-1 min-w-0 pt-1">
                            <p class="text-sm font-medium text-gray-300 mb-1">Código de verificación</p>
                            <p class="text-xs text-gray-500 leading-relaxed">Escanee este código QR con su teléfono para descargar la planilla de prosecución firmada digitalmente.</p>
                        </div>
                    </div>

                    <div class="space-y-2">
                        @if($downloadUrl)
                            <x-button href="{{ $downloadUrl }}" xl positive label="Descargar Planilla"
                                class="w-full !bg-green-800 hover:!bg-green-900 border-2 !border-green-900 shadow-lg" />
                        @endif

                        <x-button wire:click="resetWizard" xl flat white label="Nueva Consulta"
                            class="w-full !border-gray-700 hover:!bg-gray-800" />
                    </div>
                </div>
            @endif

        </div>
    </div>

</div>
