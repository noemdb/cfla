@extends('layouts.dashboard')

@section('title', 'Nueva Encuesta - Votaciones')

@section('content')
    <div class="fade-in max-w-4xl mx-auto">
        <!-- Header -->
        <div class="mb-10 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div class="flex items-center gap-4">
                <a href="{{ route('admin.voting.dashboard') }}"
                    class="p-2 text-gray-400 hover:text-emerald-400 bg-white/5 hover:bg-emerald-500/10 rounded-xl border border-white/5 transition-all duration-300">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                </a>
                <div>
                    <h1 class="text-3xl font-extrabold text-white mb-1">Nueva Encuesta</h1>
                    <p class="text-emerald-400 font-medium">Configura una nueva votación paso a paso.</p>
                </div>
            </div>
        </div>

        <form action="{{ route('admin.voting.polls.store') }}" method="POST" class="space-y-8">
            @csrf

            <!-- Información General -->
            <div
                class="diagnostic-card bg-gray-900/40 backdrop-blur-md border border-white/5 p-8 rounded-3xl relative overflow-hidden group">
                <div
                    class="absolute top-0 right-0 w-32 h-32 bg-emerald-500/5 blur-3xl -mr-16 -mt-16 group-hover:bg-emerald-500/10 transition-all duration-500">
                </div>

                <h2 class="text-xl font-bold text-white mb-6 flex items-center gap-2">
                    <span
                        class="w-8 h-8 bg-emerald-500/10 rounded-lg flex items-center justify-center text-emerald-400 text-sm font-bold">01</span>
                    Información General
                </h2>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div class="space-y-2">
                        <label for="title" class="text-sm font-bold text-gray-400 uppercase tracking-widest ml-1">
                            Título de la Encuesta
                        </label>
                        <input type="text" id="title" name="title" value="{{ old('title') }}"
                            class="w-full bg-gray-800/50 border border-white/10 text-white rounded-2xl px-5 py-4 focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500/50 transition-all outline-none placeholder:text-gray-600 @error('title') border-red-500/50 @enderror"
                            placeholder="Ej: ¿Quién debería ser el representante?" required>
                        @error('title')
                            <p class="text-xs text-red-400 font-medium ml-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="space-y-2">
                        <label for="time_active" class="text-sm font-bold text-gray-400 uppercase tracking-widest ml-1">
                            Duración (minutos)
                        </label>
                        <div class="relative">
                            <input type="number" id="time_active" name="time_active" value="{{ old('time_active', 60) }}"
                                min="1" max="10080"
                                class="w-full bg-gray-800/50 border border-white/10 text-white rounded-2xl px-5 py-4 focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500/50 transition-all outline-none placeholder:text-gray-600 @error('time_active') border-red-500/50 @enderror"
                                required>
                            <div class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-500 font-bold text-xs">MIN</div>
                        </div>
                        <p class="text-[10px] text-gray-500 font-medium ml-1 uppercase tracking-tight">
                            La encuesta se cerrará automáticamente al expirar este tiempo.
                        </p>
                        @error('time_active')
                            <p class="text-xs text-red-400 font-medium ml-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Opciones de Votación -->
            <div
                class="diagnostic-card bg-gray-900/40 backdrop-blur-md border border-white/5 p-8 rounded-3xl relative overflow-hidden group">
                <div
                    class="absolute top-0 right-0 w-32 h-32 bg-blue-500/5 blur-3xl -mr-16 -mt-16 group-hover:bg-blue-500/10 transition-all duration-500">
                </div>

                <h2 class="text-xl font-bold text-white mb-6 flex items-center gap-2">
                    <span
                        class="w-8 h-8 bg-blue-500/10 rounded-lg flex items-center justify-center text-blue-400 text-sm font-bold">02</span>
                    Opciones de Votación
                </h2>

                <div id="options-container" class="space-y-4 mb-6">
                    @for ($i = 0; $i < max(2, count(old('options', []))); $i++)
                        <div class="option-row group/row">
                            <div class="flex gap-3">
                                <div class="flex-1 space-y-2">
                                    <div class="relative">
                                        <div
                                            class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-600 font-bold text-xs">
                                            {{ $i + 1 }}</div>
                                        <input type="text" name="options[{{ $i }}][label]"
                                            value="{{ old("options.{$i}.label") }}"
                                            class="w-full bg-white/5 border border-white/10 text-white rounded-2xl pl-10 pr-5 py-4 focus:ring-2 focus:ring-blue-500/50 focus:border-blue-500/50 transition-all outline-none placeholder:text-gray-700"
                                            placeholder="Introduce la opción..." required>
                                    </div>
                                </div>
                                @if ($i >= 2)
                                    <button type="button" onclick="removeOption(this)"
                                        class="p-4 text-red-400 hover:text-white bg-red-500/10 hover:bg-red-500 rounded-2xl border border-red-500/20 transition-all duration-300 group/del">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                            </path>
                                        </svg>
                                    </button>
                                @endif
                            </div>
                        </div>
                    @endfor
                </div>

                <button type="button" onclick="addOption()"
                    class="w-full flex items-center justify-center gap-2 px-6 py-4 bg-white/5 hover:bg-white/10 text-gray-400 hover:text-white rounded-2xl border border-white/5 border-dashed transition-all duration-300 font-bold text-sm">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    AÑADIR OTRA OPCIÓN
                </button>

                @error('options')
                    <p class="mt-4 text-sm text-red-400 font-medium ml-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Action Buttons -->
            <div class="flex flex-col sm:flex-row gap-4 pt-4">
                <button type="submit"
                    class="flex-1 px-8 py-4 bg-emerald-500 hover:bg-emerald-600 text-white rounded-2xl transition-all duration-300 shadow-xl shadow-emerald-500/20 font-bold uppercase tracking-widest text-sm">
                    Crear Encuesta
                </button>
                <a href="{{ route('admin.voting.dashboard') }}"
                    class="px-8 py-4 bg-white/5 hover:bg-white/10 text-gray-400 hover:text-white rounded-2xl border border-white/10 transition-all duration-300 font-bold uppercase tracking-widest text-sm text-center">
                    Cancelar
                </a>
            </div>
        </form>
    </div>
    </div>

@endsection

@section('script')
    @parent
    <script>
        let optionIndex = {{ max(2, count(old('options', []))) }};

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
                                class="w-full bg-white/5 border border-white/10 text-white rounded-2xl pl-10 pr-5 py-4 focus:ring-2 focus:ring-blue-500/50 focus:border-blue-500/50 transition-all outline-none placeholder:text-gray-700"
                                placeholder="Introduce la opción..."
                                required
                            >
                        </div>
                    </div>
                    <button type="button" onclick="removeOption(this)" class="p-4 text-red-400 hover:text-white bg-red-500/10 hover:bg-red-500 rounded-2xl border border-red-500/20 transition-all duration-300 group/del">
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
    </script>
@endsection
