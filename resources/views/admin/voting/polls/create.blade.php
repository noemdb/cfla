@extends('layouts.voting')

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <div class="bg-white shadow-sm border-b">
        <div class="container mx-auto px-4 py-6">
            <div class="flex items-center gap-4">
                <a href="{{ route('admin.voting.dashboard') }}" class="text-gray-600 hover:text-gray-900">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                </a>
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Nueva Encuesta</h1>
                    <p class="text-gray-600 mt-1">Crea una nueva encuesta para votación</p>
                </div>
            </div>
        </div>
    </div>

    <div class="container mx-auto px-4 py-8">
        <div class="max-w-2xl mx-auto">
            <form action="{{ route('admin.voting.polls.store') }}" method="POST" class="space-y-6">
                @csrf

                <!-- Información General -->
                <div class="bg-white rounded-lg shadow p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Información General</h2>
                    <div class="space-y-4">
                        <div>
                            <label for="title" class="block text-sm font-medium text-gray-700 mb-1">
                                Título de la Encuesta
                            </label>
                            <input
                                type="text"
                                id="title"
                                name="title"
                                value="{{ old('title') }}"
                                class="w-full px-3 py-2 border rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('title') border-red-500 @enderror"
                                placeholder="¿Cuál es tu color favorito?"
                                required
                            >
                            @error('title')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="time_active" class="block text-sm font-medium text-gray-700 mb-1">
                                Duración (minutos)
                            </label>
                            <input
                                type="number"
                                id="time_active"
                                name="time_active"
                                value="{{ old('time_active', 60) }}"
                                min="1"
                                max="10080"
                                class="w-full px-3 py-2 border rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('time_active') border-red-500 @enderror"
                                required
                            >
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
                    <div id="options-container" class="space-y-4">
                        @for($i = 0; $i < max(2, count(old('options', []))); $i++)
                            <div class="option-row flex gap-2">
                                <div class="flex-1">
                                    <label for="option-{{ $i }}" class="block text-sm font-medium text-gray-700 mb-1">
                                        Opción {{ $i + 1 }}
                                    </label>
                                    <input
                                        type="text"
                                        id="option-{{ $i }}"
                                        name="options[{{ $i }}][label]"
                                        value="{{ old("options.{$i}.label") }}"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                                        placeholder="Opción {{ $i + 1 }}"
                                        required
                                    >
                                </div>
                                @if($i >= 2)
                                    <button type="button" onclick="removeOption(this)" class="mt-6 px-3 py-2 text-red-600 hover:text-red-800 transition-colors">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                        </svg>
                                    </button>
                                @endif
                            </div>
                        @endfor
                    </div>

                    <button type="button" onclick="addOption()" class="mt-4 w-full px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50 transition-colors">
                        <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        Agregar Opción
                    </button>

                    @error('options')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Botones -->
                <div class="flex gap-4">
                    <button type="submit" class="flex-1 px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors">
                        Crear Encuesta
                    </button>
                    <a href="{{ route('admin.voting.dashboard') }}" class="px-4 py-2 border border-gray-300 text-gray-700 rounded-md hover:bg-gray-50 transition-colors">
                        Cancelar
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
let optionIndex = {{ max(2, count(old('options', []))) }};

function addOption() {
    const container = document.getElementById('options-container');
    const optionRow = document.createElement('div');
    optionRow.className = 'option-row flex gap-2';
    optionRow.innerHTML = `
        <div class="flex-1">
            <label for="option-${optionIndex}" class="block text-sm font-medium text-gray-700 mb-1">
                Opción ${optionIndex + 1}
            </label>
            <input
                type="text"
                id="option-${optionIndex}"
                name="options[${optionIndex}][label]"
                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                placeholder="Opción ${optionIndex + 1}"
                required
            >
        </div>
        <button type="button" onclick="removeOption(this)" class="mt-6 px-3 py-2 text-red-600 hover:text-red-800 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
            </svg>
        </button>
    `;
    container.appendChild(optionRow);
    optionIndex++;
}

function removeOption(button) {
    const optionRows = document.querySelectorAll('.option-row');
    if (optionRows.length > 2) {
        button.closest('.option-row').remove();
    }
}
</script>
@endsection
