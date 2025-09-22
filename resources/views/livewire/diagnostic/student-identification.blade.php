<div class=" flex items-center justify-center bg-gray-900 px-4  py-4">
    <div class="max-w-md w-full space-y-2">
        <!-- Header -->
        <div class="text-center">
            <div class="mx-auto h-20 w-20 bg-emerald-600 rounded-full flex items-center justify-center mb-6">
                <svg class="h-10 w-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                </svg>
            </div>
            <h2 class="text-3xl font-bold text-white mb-2">
                Identificación de Estudiante.
            </h2>
            <p class="text-gray-400">
                Ingresa tu número de cédula para acceder al diagnóstico educativo
            </p>
        </div>

        <!-- Formulario -->
        <div class="bg-gray-800 rounded-xl p-8 shadow-2xl border border-gray-700">
            <form wire:submit.prevent="verifyStudent" class="space-y-6">
                <!-- Campo de cédula -->
                <div>
                    <label for="studentCi" class="block text-sm font-medium text-gray-300 mb-2">
                        Número de Cédula
                    </label>
                    <div class="relative">
                        <input type="text" id="studentCi" wire:model.defer="studentCi" placeholder="Ej: 12345678"
                            class="w-full px-4 py-3 bg-gray-700 border border-gray-600 rounded-lg text-white placeholder-gray-400 focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition-colors"
                            autocomplete="off">
                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center">
                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V4a2 2 0 114 0v2m-4 0a2 2 0 104 0m-4 0v2m0 0h4">
                                </path>
                            </svg>
                        </div>
                    </div>
                    @error('studentCi')
                        <p class="mt-2 text-sm text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Botón de verificación -->
                <button type="submit" wire:loading.attr="disabled"
                    class="w-full bg-emerald-600 hover:bg-emerald-700 disabled:opacity-50 disabled:cursor-not-allowed text-white font-semibold py-3 px-4 rounded-lg transition-colors flex items-center justify-center space-x-2">
                    <span wire:loading.remove>Verificar Identidad</span>
                    <span wire:loading class="flex items-center space-x-2">
                        <svg class="animate-spin h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor"
                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                            </path>
                        </svg>
                        <span>Verificando...</span>
                    </span>
                </button>
            </form>

            <!-- Información adicional -->
            <div class="mt-6 p-4 bg-gray-700/50 rounded-lg">
                <div class="flex items-start space-x-3">
                    <svg class="h-5 w-5 text-emerald-400 mt-0.5" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <div class="text-sm text-gray-300">
                        <p class="font-medium mb-1">Información importante:</p>
                        <ul class="space-y-1 text-gray-400">
                            <li>• Ingresa tu cédula sin puntos ni espacios</li>
                            <li>• Solo números, sin letras ni caracteres especiales</li>
                            <li>• Si tienes problemas, contacta a tu docente</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
