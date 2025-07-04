@extends('layouts.vote')

@section('title', 'Código QR de Participación')

@section('content')
    <div class="container mx-auto px-4 max-w-2xl">
        <div
            class="bg-gray-800/90 backdrop-blur-sm rounded-2xl shadow-2xl border border-gray-700 overflow-hidden glow-green-strong">
            <!-- Header -->
            <div
                class="bg-gradient-to-r from-gray-900 via-emerald-900 to-gray-900 px-6 py-8 text-center text-white shadow-2xl border-b border-emerald-800/50">
                <div
                    class="w-20 h-20 bg-gradient-to-br from-emerald-500/30 to-green-600/30 rounded-full flex items-center justify-center mx-auto mb-6 shadow-lg border border-emerald-500/50">
                    <svg class="w-10 h-10 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0z">
                        </path>
                    </svg>
                </div>
                <h1 class="text-2xl md:text-3xl font-bold mb-2 text-white">¡Participación Confirmada!</h1>
                <p class="text-emerald-200">Tu código QR de participación está listo</p>
            </div>

            <div class="p-6 space-y-6">
                <!-- Información de la encuesta -->
                <div
                    class="bg-gradient-to-r from-gray-800/50 to-emerald-900/20 rounded-xl p-4 border border-gray-600/50 shadow-lg backdrop-blur-sm">
                    <h3 class="font-semibold text-white mb-2">{{ $poll->title }}</h3>
                    <div class="text-sm text-gray-300 space-y-1">
                        <p><span class="text-emerald-400">Tu voto:</span> {{ $vote->option->label }}</p>
                        <p><span class="text-emerald-400">Fecha:</span> {{ $session->created_at->format('d/m/Y H:i') }}</p>
                        <p><span class="text-emerald-400">ID de sesión:</span> {{ substr($session->uuid, 0, 8) }}...</p>
                    </div>
                </div>

                <!-- Código QR -->
                <div class="text-center">
                    <div class="bg-white rounded-xl p-6 inline-block shadow-lg border border-gray-300">
                        {!! $qrCode !!}
                    </div>
                    <p class="text-gray-300 text-sm mt-3">Escanea este código para ver los detalles de tu participación</p>
                </div>

                <!-- Enlace directo -->
                <div
                    class="bg-gradient-to-r from-gray-800/50 to-gray-700/50 border border-gray-600/50 rounded-xl p-4 shadow-lg backdrop-blur-sm">
                    <div class="flex items-center justify-between">
                        <div class="flex-1 mr-3">
                            <p class="text-white font-medium mb-1">Enlace directo:</p>
                            <p class="text-gray-300 text-sm break-all">{{ $participationUrl }}</p>
                        </div>
                        <button onclick="copyToClipboard('{{ $participationUrl }}')"
                            class="bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2 rounded-lg transition-colors duration-200 flex items-center space-x-2 flex-shrink-0">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z">
                                </path>
                            </svg>
                            <span>Copiar</span>
                        </button>
                    </div>
                </div>

                <!-- Botones de acción -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <a href="{{ route('poll.participation.show', ['uuid' => $session->uuid]) }}"
                        class="bg-gradient-to-r from-emerald-600 via-green-700 to-emerald-600 hover:from-emerald-500 hover:via-green-600 hover:to-emerald-500 text-white font-bold py-3 px-6 rounded-xl transition-all duration-300 transform hover:scale-[1.02] focus:outline-none focus:ring-4 focus:ring-emerald-500/50 shadow-xl hover:shadow-2xl text-center flex items-center justify-center space-x-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z">
                            </path>
                        </svg>
                        <span>Ver Datos Completos</span>
                    </a>

                    <button onclick="window.print()"
                        class="bg-gradient-to-r from-gray-600 via-gray-700 to-gray-600 hover:from-gray-500 hover:via-gray-600 hover:to-gray-500 text-white font-bold py-3 px-6 rounded-xl transition-all duration-300 transform hover:scale-[1.02] focus:outline-none focus:ring-4 focus:ring-gray-500/50 shadow-xl hover:shadow-2xl flex items-center justify-center space-x-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z">
                            </path>
                        </svg>
                        <span>Imprimir QR</span>
                    </button>
                </div>

                <!-- Información adicional -->
                <div
                    class="bg-gradient-to-r from-blue-900/20 to-indigo-900/20 border border-blue-700/50 rounded-xl p-4 shadow-lg backdrop-blur-sm">
                    <div class="flex items-start space-x-3">
                        <svg class="w-5 h-5 text-blue-400 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <div class="text-sm text-gray-300 space-y-1">
                            <p class="font-medium text-white">Información importante:</p>
                            <ul class="space-y-1 text-xs">
                                <li>• Guarda este código QR como comprobante de tu participación</li>
                                <li>• Puedes acceder a los datos completos escaneando el código o usando el enlace</li>
                                <li>• Tu voto es completamente anónimo y seguro</li>
                                <li>• Los resultados finales estarán disponibles cuando termine la votación</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('script')
    @parent
    <script>
        function copyToClipboard(text) {
            navigator.clipboard.writeText(text).then(function() {
                // Mostrar notificación de éxito
                const notification = document.createElement('div');
                notification.className =
                    'fixed top-4 right-4 bg-emerald-600 text-white px-4 py-2 rounded-lg shadow-lg z-50 transition-opacity duration-300';
                notification.textContent = '¡Enlace copiado al portapapeles!';
                document.body.appendChild(notification);

                setTimeout(() => {
                    notification.style.opacity = '0';
                    setTimeout(() => {
                        document.body.removeChild(notification);
                    }, 300);
                }, 2000);
            }).catch(function(err) {
                console.error('Error al copiar: ', err);
                alert('No se pudo copiar el enlace. Por favor, cópialo manualmente.');
            });
        }
    </script>
@endsection
