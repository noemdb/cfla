@extends('layouts.dashboard')

@section('title', 'Editar Encuesta - Votaciones')

@section('content')
    <div class="min-h-screen bg-gray-50">
        <!-- Header -->
        <div class="bg-white shadow-sm border-b">
            <div class="container mx-auto px-4 py-6">
                <div class="flex items-center gap-4">
                    <a href="{{ route('admin.voting.dashboard') }}" class="text-gray-600 hover:text-gray-900">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                    </a>
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900">Editar Encuesta</h1>
                        <p class="text-gray-600 mt-1">Modifica los datos de la encuesta</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="container mx-auto px-4 py-8">
            <div class="max-w-2xl mx-auto">
                <!-- Información de la encuesta -->
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 text-blue-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <div>
                            <p class="text-sm font-medium text-blue-800">
                                Token de acceso: <code
                                    class="bg-white px-2 py-1 rounded text-xs">{{ $poll->access_token }}</code>
                            </p>
                            <p class="text-xs text-blue-600 mt-1">
                                Estado:
                                <span class="font-medium {{ $poll->enable ? 'text-green-600' : 'text-gray-600' }}">
                                    {{ $poll->enable ? 'Activa' : 'Inactiva' }}
                                </span>
                                @if ($poll->enable && $poll->date)
                                    | Iniciada: {{ $poll->date->format('d/m/Y H:i') }}
                                @endif
                            </p>
                        </div>
                    </div>
                </div>

                @if ($poll->enable)
                    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-6">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 text-yellow-600 mr-2" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z">
                                </path>
                            </svg>
                            <p class="text-sm text-yellow-800">
                                <strong>Advertencia:</strong> Esta encuesta está activa. Los cambios pueden afectar las
                                votaciones en curso.
                            </p>
                        </div>
                    </div>
                @endif

                <form action="{{ route('admin.voting.polls.update', $poll) }}" method="POST" class="space-y-6">
                    @csrf
                    @method('PUT')

                    <!-- Información General -->
                    <div class="bg-white rounded-lg shadow p-6">
                        <h2 class="text-lg font-semibold text-gray-900 mb-4">Información General</h2>
                        <div class="space-y-4">
                            <div>
                                <label for="title" class="block text-sm font-medium text-gray-700 mb-1">
                                    Título de la Encuesta
                                </label>
                                <input type="text" id="title" name="title" value="{{ old('title', $poll->title) }}"
                                    class="w-full px-3 py-2 border  rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('title') border-red-500 @enderror"
                                    placeholder="¿Cuál es tu color favorito?" required>
                                @error('title')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="time_active" class="block text-sm font-medium text-gray-700 mb-1">
                                    Duración (minutos)
                                </label>
                                <input type="number" id="time_active" name="time_active"
                                    value="{{ old('time_active', $poll->time_active) }}" min="1" max="10080"
                                    class="w-full px-3 py-2 border rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('time_active') border-red-500 @enderror"
                                    required>
                                <p class="mt-1 text-sm text-gray-500">
                                    La encuesta se cerrará automáticamente después de este tiempo
                                </p>
                                @error('time_active')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Opciones de Votación -->
                    <div class="bg-white rounded-lg shadow p-6">
                        <h2 class="text-lg font-semibold text-gray-900 mb-4">Opciones de Votación</h2>

                        @if ($poll->votes_count > 0)
                            <div class="bg-orange-50 border border-orange-200 rounded-lg p-4 mb-4">
                                <div class="flex items-center">
                                    <svg class="w-5 h-5 text-orange-600 mr-2" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z">
                                        </path>
                                    </svg>
                                    <p class="text-sm text-orange-800">
                                        <strong>Atención:</strong> Esta encuesta ya tiene {{ $poll->votes_count }} votos.
                                        Modificar las opciones eliminará todos los votos existentes.
                                    </p>
                                </div>
                            </div>
                        @endif

                        <div id="options-container" class="space-y-4">
                            @foreach ($poll->options as $index => $option)
                                <div class="option-row flex gap-2">
                                    <div class="flex-1">
                                        <label for="option-{{ $index }}"
                                            class="block text-sm font-medium text-gray-700 mb-1">
                                            Opción {{ $index + 1 }}
                                            @if ($option->votes_count > 0)
                                                <span class="text-xs text-gray-500">({{ $option->votes_count }}
                                                    votos)</span>
                                            @endif
                                        </label>
                                        <input type="text" id="option-{{ $index }}"
                                            name="options[{{ $index }}][label]"
                                            value="{{ old("options.{$index}.label", $option->label) }}"
                                            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                                            placeholder="Opción {{ $index + 1 }}" required>
                                    </div>
                                    @if ($poll->options->count() > 2)
                                        <button type="button" onclick="removeOption(this)"
                                            class="mt-6 px-3 py-2 text-red-600 hover:text-red-800 transition-colors">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                                </path>
                                            </svg>
                                        </button>
                                    @endif
                                </div>
                            @endforeach
                        </div>

                        <button type="button" onclick="addOption()"
                            class="mt-4 w-full px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50 transition-colors">
                            <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            </svg>
                            Agregar Opción
                        </button>

                        @error('options')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Estadísticas actuales -->
                    @if ($poll->votes_count > 0)
                        <div class="bg-white rounded-lg shadow p-6">
                            <h2 class="text-lg font-semibold text-gray-900 mb-4">Estadísticas Actuales</h2>
                            <div class="grid md:grid-cols-3 gap-4">
                                <div class="text-center p-4 bg-blue-50 rounded-lg">
                                    <div class="text-2xl font-bold text-blue-600">{{ $poll->votes_count }}</div>
                                    <div class="text-sm text-blue-800">Votos Totales</div>
                                </div>
                                <div class="text-center p-4 bg-green-50 rounded-lg">
                                    <div class="text-2xl font-bold text-green-600">{{ $poll->options->count() }}</div>
                                    <div class="text-sm text-green-800">Opciones</div>
                                </div>
                                <div class="text-center p-4 bg-purple-50 rounded-lg">
                                    <div class="text-2xl font-bold text-purple-600">
                                        {{ $poll->votes_count > 0 ? round($poll->votes_count / $poll->options->count(), 1) : 0 }}
                                    </div>
                                    <div class="text-sm text-purple-800">Promedio por Opción</div>
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Botones -->
                    <div class="flex gap-4">
                        <button type="submit"
                            class="flex-1 px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors">
                            Actualizar Encuesta
                        </button>
                        <a href="{{ route('admin.voting.dashboard') }}"
                            class="px-4 py-2 border border-gray-300 text-gray-700 rounded-md hover:bg-gray-50 transition-colors">
                            Cancelar
                        </a>
                    </div>

                    @if ($poll->votes_count > 0)
                        <div class="text-center">
                            <p class="text-sm text-gray-500">
                                Al actualizar esta encuesta, se eliminarán todos los votos existentes.
                            </p>
                        </div>
                    @endif
                </form>
            </div>
        </div>
    </div>

    <script>
        let optionIndex = {{ $poll->options->count() }};

        function addOption() {
            const container = document.getElementById('options-container');
            const optionRow = document.createElement('div');
            optionRow.className = 'option-row group/row';
            optionRow.innerHTML = `
                <div class="flex gap-3">
                    <div class="flex-1 space-y-2">
                        <div class="relative">
                            <div class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-600 font-bold text-xs">${optionIndex + 1}</div>
                            <input
                                type="text"
                                name="options[${optionIndex}][label]"
                                class="w-full bg-white/5 border border-white/10 text-white rounded-2xl pl-10 pr-5 py-4 focus:ring-2 focus:ring-blue-500/50 focus:border-blue-500/50 transition-all outline-none"
                                placeholder="Introduce la opción..."
                                required
                            >
                        </div>
                    </div>
                    <button type="button" onclick="removeOption(this)" class="p-4 text-red-400 hover:text-white bg-red-500/10 hover:bg-red-500 rounded-2xl border border-red-500/20 transition-all duration-300">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                        </svg>
                    </button>
                </div>
            `;
            container.appendChild(optionRow);
            optionIndex++;
        }

        function removeOption(button) {
            const optionRows = document.querySelectorAll('.option-row');
            if (optionRows.length > 2) {
                button.closest('.option-row').remove();
                updateIndices();
            }
        }

        function updateIndices() {
            const rows = document.querySelectorAll('.option-row');
            rows.forEach((row, idx) => {
                const label = row.querySelector('.absolute');
                const input = row.querySelector('input');
                label.textContent = idx + 1;
                input.name = `options[${idx}][label]`;
            });
            optionIndex = rows.length;
        }

        // Confirmación antes de enviar si hay votos
        @if ($poll->votes_count > 0)
            document.querySelector('form').addEventListener('submit', function(e) {
                if (!confirm(
                        '¿Estás seguro? Esta acción eliminará todos los votos existentes ({{ $poll->votes_count }} votos).'
                        )) {
                    e.preventDefault();
                }
            });
        @endif
    </script>

    {{-- @if (session('success'))
    <x-notification
        title="Éxito"
        description="{{ session('success') }}"
        icon="success"
    />
@endif --}}

    {{-- @if (session('error'))
    <x-notification
        title="Error"
        description="{{ session('error') }}"
        icon="error"
    />
@endif --}}
@endsection
