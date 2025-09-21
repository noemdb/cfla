<div class="min-h-screen bg-gray-900">
    <div class="container mx-auto px-4 py-8">
        <!-- Header -->
        <div class="text-center mb-12">
            <div class="w-20 h-20 bg-green-600 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
            </div>
            <h1 class="text-3xl font-bold text-white mb-2">
                ¡Diagnóstico Completado!
            </h1>
            <p class="text-gray-400">
                Has completado el diagnóstico para {{ $selectedPensum->asignatura->full_name ?? 'esta área' }}
            </p>
        </div>

        <!-- Resumen de resultados -->
        <div class="max-w-4xl mx-auto">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <div class="bg-gray-800 p-6 rounded-xl text-center">
                    <div class="text-3xl font-bold text-green-400 mb-2">
                        {{ count($questions) }}
                    </div>
                    <div class="text-gray-400">Preguntas Respondidas</div>
                </div>

                <div class="bg-gray-800 p-6 rounded-xl text-center">
                    <div class="text-3xl font-bold text-blue-400 mb-2">
                        {{ $progress }}%
                    </div>
                    <div class="text-gray-400">Progreso Completado</div>
                </div>

                <div class="bg-gray-800 p-6 rounded-xl text-center">
                    <div class="text-3xl font-bold text-purple-400 mb-2">
                        {{ $currentSession ? $currentSession->created_at->diffInMinutes($currentSession->completado_at) : 0 }}
                    </div>
                    <div class="text-gray-400">Minutos Invertidos</div>
                </div>
            </div>

            <!-- Acciones -->
            <div class="text-center space-y-4">
                <button wire:click="backToDashboard"
                    class="bg-green-600 hover:bg-green-700 text-white px-8 py-3 rounded-lg text-lg">
                    Volver al Dashboard
                </button>

                <div>
                    <button class="text-gray-400 hover:text-white underline">
                        Ver Respuestas Detalladas
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
