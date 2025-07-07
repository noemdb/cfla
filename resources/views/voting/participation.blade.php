@extends('layouts.vote')

@section('title', 'Detalles de Participación - ' . $poll->title)

@section('content')
    <div class="min-h-screen bg-gradient-to-br from-gray-900 via-green-900 to-gray-900 py-8 px-4">
        <div class="container mx-auto max-w-4xl">
            <!-- Header -->
            <div class="text-center mb-8">
                <div
                    class="inline-flex items-center px-4 py-2 bg-emerald-500/20 backdrop-blur-sm rounded-full text-emerald-300 text-sm font-medium border border-emerald-500/30 mb-4">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    Certificado de Participación
                </div>
                <h1 class="text-3xl font-bold text-white mb-2">{{ $poll->title }}</h1>
                <p class="text-gray-300">Detalles de tu participación y resultados actuales</p>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Certificado de participación -->
                <div class="lg:col-span-2">
                    <div
                        class="bg-gray-800/90 backdrop-blur-sm rounded-2xl shadow-2xl border border-gray-700 overflow-hidden print-section">
                        <!-- Header del certificado -->
                        <div
                            class="bg-gradient-to-r from-gray-900 via-emerald-900 to-gray-900 px-8 py-6 text-center border-b border-emerald-800/50">
                            <div
                                class="w-16 h-16 bg-gradient-to-br from-emerald-500/30 to-green-600/30 rounded-full flex items-center justify-center mx-auto mb-4 border border-emerald-500/50">
                                <svg class="w-8 h-8 text-emerald-400" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <h2 class="text-2xl font-bold text-white mb-2">Certificado de Participación</h2>
                            <p class="text-emerald-200">Sistema de Votación Digital</p>
                        </div>

                        <!-- Contenido del certificado -->
                        <div class="p-8">
                            <div class="text-center mb-8">
                                <p class="text-gray-300 text-lg mb-4">
                                    Se certifica que el portador de este documento ha participado exitosamente en la
                                    encuesta:
                                </p>
                                <h3
                                    class="text-2xl font-bold text-white mb-6 px-4 py-2 bg-emerald-500/20 rounded-lg border border-emerald-500/30">
                                    "{{ $poll->title }}"
                                </h3>
                            </div>

                            <!-- Detalles de participación -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                                <div
                                    class="bg-gradient-to-r from-gray-700/50 to-emerald-900/20 rounded-xl p-4 border border-gray-600/50">
                                    <div class="flex items-center mb-2">
                                        <svg class="w-5 h-5 text-emerald-400 mr-2" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        <span class="text-emerald-400 font-medium">Fecha de Participación</span>
                                    </div>
                                    <p class="text-white font-bold">{{ $session->created_at->format('d/m/Y') }}</p>
                                    <p class="text-gray-300 text-sm">{{ $session->created_at->format('H:i:s') }}</p>
                                </div>

                                <div
                                    class="bg-gradient-to-r from-gray-700/50 to-emerald-900/20 rounded-xl p-4 border border-gray-600/50">
                                    <div class="flex items-center mb-2">
                                        <svg class="w-5 h-5 text-emerald-400 mr-2" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z">
                                            </path>
                                        </svg>
                                        <span class="text-emerald-400 font-medium">ID de Sesión</span>
                                    </div>
                                    <p class="text-white font-bold font-mono">{{ substr($session->uuid, 0, 8) }}...</p>
                                    <p class="text-gray-300 text-sm">Identificador único</p>
                                </div>

                                <div
                                    class="bg-gradient-to-r from-gray-700/50 to-emerald-900/20 rounded-xl p-4 border border-gray-600/50">
                                    <div class="flex items-center mb-2">
                                        <svg class="w-5 h-5 text-emerald-400 mr-2" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4">
                                            </path>
                                        </svg>
                                        <span class="text-emerald-400 font-medium">Tu Elección</span>
                                    </div>
                                    <p class="text-white font-bold">{{ $vote->option->label }}</p>
                                    <p class="text-gray-300 text-sm">Opción seleccionada</p>
                                </div>

                                <div
                                    class="bg-gradient-to-r from-gray-700/50 to-emerald-900/20 rounded-xl p-4 border border-gray-600/50">
                                    <div class="flex items-center mb-2">
                                        <svg class="w-5 h-5 text-emerald-400 mr-2" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z">
                                            </path>
                                        </svg>
                                        <span class="text-emerald-400 font-medium">Total Participantes</span>
                                    </div>
                                    <p class="text-white font-bold">{{ $totalVotes }}</p>
                                    <p class="text-gray-300 text-sm">Votos registrados</p>
                                </div>
                            </div>

                            <!-- Firma digital -->
                            <div class="border-t border-gray-600 pt-6 text-center">
                                <p class="text-gray-400 text-sm mb-2">Certificado generado digitalmente</p>
                                <p class="text-gray-500 text-xs">{{ now()->format('d/m/Y H:i:s') }} - Sistema de Votación
                                    v1.0</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Resultados en tiempo real -->
                <div class="lg:col-span-1">
                    <div
                        class="bg-gray-800/90 backdrop-blur-sm rounded-2xl shadow-2xl border border-gray-700 overflow-hidden">
                        <div
                            class="bg-gradient-to-r from-gray-900 via-blue-900 to-gray-900 px-6 py-4 border-b border-blue-800/50">
                            <h3 class="text-lg font-bold text-white flex items-center">
                                <svg class="w-5 h-5 mr-2 text-blue-400" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z">
                                    </path>
                                </svg>
                                Resultados Actuales
                            </h3>
                            <p class="text-blue-200 text-sm">Actualizado en tiempo real</p>
                        </div>

                        <div class="p-6">
                            <div class="space-y-4">
                                @foreach ($optionStats as $stat)
                                    <div class="relative">
                                        <div class="flex items-center justify-between mb-2">
                                            <div class="flex items-center">
                                                <span class="text-white font-medium text-sm">{{ $stat['label'] }}</span>
                                                @if ($stat['is_user_choice'])
                                                    <span
                                                        class="ml-2 px-2 py-1 bg-emerald-500/20 text-emerald-300 text-xs rounded-full border border-emerald-500/30">
                                                        Tu voto
                                                    </span>
                                                @endif
                                            </div>
                                            <span class="text-gray-300 text-sm font-bold">{{ $stat['percentage'] }}%</span>
                                        </div>
                                        <div class="w-full bg-gray-700 rounded-full h-3 overflow-hidden">
                                            <div class="h-full rounded-full transition-all duration-1000 ease-out {{ $stat['is_user_choice'] ? 'bg-gradient-to-r from-emerald-500 to-green-500' : 'bg-gradient-to-r from-blue-500 to-indigo-500' }}"
                                                style="width: {{ $stat['percentage'] }}%"></div>
                                        </div>
                                        <div class="flex justify-between mt-1">
                                            <span class="text-xs text-gray-400">{{ $stat['votes'] }} votos</span>
                                            <span
                                                class="text-xs text-gray-400">{{ $totalVotes > 0 ? number_format(($stat['votes'] / $totalVotes) * 100, 1) : 0 }}%</span>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            <div class="mt-6 pt-4 border-t border-gray-600">
                                <div class="text-center">
                                    <p class="text-gray-400 text-sm">Total de participantes</p>
                                    <p class="text-2xl font-bold text-white">{{ $totalVotes }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Acciones -->
                    <div class="mt-6 space-y-3">
                        <button onclick="shareResults()"
                            class="w-full flex items-center justify-center px-4 py-3 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white font-medium rounded-xl transition-all duration-300 transform hover:scale-[1.02] focus:outline-none focus:ring-4 focus:ring-blue-500/50 shadow-lg">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.367 2.684 3 3 0 00-5.367-2.684z">
                                </path>
                            </svg>
                            Compartir Participación
                        </button>

                        <button onclick="printCertificate()"
                            class="w-full flex items-center justify-center px-4 py-3 bg-gradient-to-r from-gray-600 to-gray-700 hover:from-gray-700 hover:to-gray-800 text-white font-medium rounded-xl transition-all duration-300 transform hover:scale-[1.02] focus:outline-none focus:ring-4 focus:ring-gray-500/50 shadow-lg">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z">
                                </path>
                            </svg>
                            Imprimir Certificado
                        </button>

                        <button onclick="refreshResults()"
                            class="w-full flex items-center justify-center px-4 py-3 bg-gradient-to-r from-emerald-600 to-green-600 hover:from-emerald-700 hover:to-green-700 text-white font-medium rounded-xl transition-all duration-300 transform hover:scale-[1.02] focus:outline-none focus:ring-4 focus:ring-emerald-500/50 shadow-lg">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15">
                                </path>
                            </svg>
                            Actualizar Resultados
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    @parent
    <script>
        async function shareResults() {
            const shareData = {
                title: 'Mi Participación en {{ $poll->title }}',
                text: 'He participado en esta encuesta. Ve los detalles de mi participación y los resultados actuales:',
                url: window.location.href
            };

            try {
                if (navigator.share) {
                    await navigator.share(shareData);
                } else {
                    await navigator.clipboard.writeText(shareData.url);
                    showNotification('Enlace copiado al portapapeles para compartir', 'success');
                }
            } catch (err) {
                // Fallback manual
                const textArea = document.createElement('textarea');
                textArea.value = shareData.url;
                document.body.appendChild(textArea);
                textArea.select();
                document.execCommand('copy');
                document.body.removeChild(textArea);
                showNotification('Enlace copiado al portapapeles', 'success');
            }
        }

        function printCertificate() {
            window.print();
        }

        function refreshResults() {
            showNotification('Actualizando resultados...', 'info');
            setTimeout(() => {
                window.location.reload();
            }, 1000);
        }

        function showNotification(message, type = 'info') {
            const notification = document.createElement('div');
            notification.className = `fixed top-4 right-4 px-6 py-3 rounded-lg shadow-lg z-50 transition-opacity duration-300 ${
        type === 'success' ? 'bg-green-600 text-white' :
        type === 'error' ? 'bg-red-600 text-white' :
        type === 'info' ? 'bg-blue-600 text-white' :
        'bg-gray-600 text-white'
    }`;
            notification.textContent = message;

            document.body.appendChild(notification);

            setTimeout(() => {
                notification.style.opacity = '0';
                setTimeout(() => {
                    if (document.body.contains(notification)) {
                        document.body.removeChild(notification);
                    }
                }, 300);
            }, 3000);
        }

        // Auto-refresh results every 30 seconds
        setInterval(() => {
            if (document.visibilityState === 'visible') {
                refreshResults();
            }
        }, 30000);
    </script>
@endsection

@section('style')
@parent
<!-- Estilos para impresión -->
<style>
    @media print {
        body * {
            visibility: hidden;
        }

        .print-section,
        .print-section * {
            visibility: visible;
        }

        .print-section {
            position: absolute;
            left: 0;
            top: 0;
            width: 100%;
        }

        .bg-gradient-to-br,
        .bg-gray-800\/90,
        .bg-gradient-to-r {
            background: white !important;
            color: black !important;
        }

        .text-white,
        .text-gray-300,
        .text-emerald-300,
        .text-emerald-200 {
            color: black !important;
        }

        .border-gray-700,
        .border-emerald-800\/50,
        .border-gray-600 {
            border-color: #ccc !important;
        }
    }
</style>
@endsection
