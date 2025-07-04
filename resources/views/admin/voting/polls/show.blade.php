@extends('layouts.app')

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
                    <h1 class="text-3xl font-bold text-gray-900">Detalles de la Encuesta</h1>
                    <p class="text-gray-600 mt-1">Información completa y estadísticas</p>
                </div>
            </div>
        </div>
    </div>

    <div class="container mx-auto px-4 py-8">
        <div class="max-w-4xl mx-auto space-y-6">
            <!-- Información principal -->
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex justify-between items-start mb-6">
                    <div>
                        <h2 class="text-2xl font-bold text-gray-900 mb-2">{{ $poll->title }}</h2>
                        <div class="flex items-center gap-2">
                            <span class="px-3 py-1 text-sm font-medium rounded-full {{ $poll->enable ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                {{ $poll->enable ? 'Activa' : 'Inactiva' }}
                            </span>
                            @if($poll->enable && $poll->time_remaining)
                                <span class="px-3 py-1 text-sm font-medium bg-orange-100 text-orange-800 rounded-full">
                                    {{ $poll->time_remaining }}
                                </span>
                            @endif
                        </div>
                    </div>

                    <!-- Acciones rápidas -->
                    <div class="flex gap-2">
                        @if($poll->enable)
                            <form action="{{ route('admin.voting.polls.stop', $poll) }}" method="POST" class="inline">
                                @csrf
                                <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors">
                                    ⏹️ Detener
                                </button>
                            </form>
                        @else
                            <form action="{{ route('admin.voting.polls.start', $poll) }}" method="POST" class="inline">
                                @csrf
                                <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                                    ▶️ Iniciar
                                </button>
                            </form>
                        @endif

                        <a href="{{ route('admin.voting.polls.edit', $poll) }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                            ✏️ Editar
                        </a>

                        @if($poll->votes_count > 0)
                            <form action="{{ route('admin.voting.polls.reset', $poll) }}" method="POST" class="inline" onsubmit="return confirmReset()">
                                @csrf
                                <button type="submit" class="px-4 py-2 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700 transition-colors">
                                    🔄 Reset
                                </button>
                            </form>
                        @endif
                    </div>
                </div>

                <!-- Información técnica -->
                <div class="grid md:grid-cols-2 gap-6 mb-6">
                    <div class="space-y-3">
                        <div>
                            <label class="text-sm font-medium text-gray-600">Token de acceso</label>
                            <div class="flex items-center gap-2">
                                <code class="bg-gray-100 px-3 py-1 rounded text-sm">{{ $poll->access_token }}</code>
                                <button onclick="copyToClipboard('{{ $poll->access_token }}')" class="text-blue-600 hover:text-blue-800">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                                    </svg>
                                </button>
                            </div>
                        </div>

                        <div>
                            <label class="text-sm font-medium text-gray-600">Duración</label>
                            <p class="text-gray-900">{{ $poll->time_active }} minutos</p>
                        </div>

                        @if($poll->date)
                            <div>
                                <label class="text-sm font-medium text-gray-600">Fecha de inicio</label>
                                <p class="text-gray-900">{{ $poll->date->format('d/m/Y H:i:s') }}</p>
                            </div>
                        @endif
                    </div>

                    <div class="space-y-3">
                        <div>
                            <label class="text-sm font-medium text-gray-600">Creada</label>
                            <p class="text-gray-900">{{ $poll->created_at->format('d/m/Y H:i:s') }}</p>
                        </div>

                        <div>
                            <label class="text-sm font-medium text-gray-600">Última actualización</label>
                            <p class="text-gray-900">{{ $poll->updated_at->format('d/m/Y H:i:s') }}</p>
                        </div>
                    </div>
                </div>

                <!-- Enlace público -->
                @if($poll->enable)
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                        <label class="text-sm font-medium text-blue-800">Enlace público de votación</label>
                        <div class="flex items-center gap-2 mt-1">
                            <code class="bg-white px-3 py-2 rounded text-sm flex-1">{{ url('/poll/voting/' . $poll->access_token) }}</code>
                            <button onclick="copyToClipboard('{{ url('/poll/voting/' . $poll->access_token) }}')" class="px-3 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 transition-colors">
                                Copiar
                            </button>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Estadísticas -->
            <div class="grid md:grid-cols-4 gap-6">
                <div class="bg-white rounded-lg shadow p-6 text-center">
                    <div class="text-3xl font-bold text-blue-600">{{ $poll->options->count() }}</div>
                    <div class="text-sm text-gray-600">Opciones</div>
                </div>

                <div class="bg-white rounded-lg shadow p-6 text-center">
                    <div class="text-3xl font-bold text-green-600">{{ $poll->votes_count }}</div>
                    <div class="text-sm text-gray-600">Votos Totales</div>
                </div>

                <div class="bg-white rounded-lg shadow p-6 text-center">
                    <div class="text-3xl font-bold text-purple-600">{{ $poll->sessions->count() }}</div>
                    <div class="text-sm text-gray-600">Sesiones</div>
                </div>

                <div class="bg-white rounded-lg shadow p-6 text-center">
                    <div class="text-3xl font-bold text-orange-600">
                        {{ $poll->votes_count > 0 ? round($poll->votes_count / $poll->options->count(), 1) : 0 }}
                    </div>
                    <div class="text-sm text-gray-600">Promedio por Opción</div>
                </div>
            </div>

            <!-- Opciones y resultados -->
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Opciones y Resultados</h3>

                @if($poll->votes_count > 0)
                    <div class="space-y-4">
                        @foreach($poll->options as $option)
                            @php
                                $percentage = $poll->votes_count > 0 ? round(($option->votes_count / $poll->votes_count) * 100) : 0;
                            @endphp
                            <div class="space-y-2">
                                <div class="flex justify-between items-center">
                                    <span class="font-medium text-gray-900">{{ $option->label }}</span>
                                    <div class="flex items-center gap-2">
                                        <span class="text-sm text-gray-600">{{ $option->votes_count }} votos</span>
                                        <span class="px-2 py-1 bg-gray-100 text-gray-800 rounded text-sm font-medium">
                                            {{ $percentage }}%
                                        </span>
                                    </div>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-3">
                                    <div class="bg-blue-600 h-3 rounded-full transition-all duration-300" style="width: {{ $percentage }}%"></div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-8 text-gray-500">
                        <svg class="w-12 h-12 mx-auto mb-4 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                        </svg>
                        <h4 class="text-lg font-medium text-gray-900 mb-2">Sin votos registrados</h4>
                        <p>Esta encuesta aún no ha recibido votos.</p>

                        <div class="mt-4 space-y-2">
                            @foreach($poll->options as $index => $option)
                                <div class="bg-gray-50 p-3 rounded-lg text-left">
                                    <span class="font-medium">Opción {{ $index + 1 }}:</span> {{ $option->label }}
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>

            <!-- Historial de sesiones (solo si hay votos) -->
            @if($poll->sessions->count() > 0)
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Historial de Sesiones</h3>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">IP</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fecha</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Expira</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($poll->sessions->take(10) as $session)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-2 py-1 text-xs font-medium rounded-full {{ $session->voted ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                                {{ $session->voted ? 'Votó' : 'Sin votar' }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ $session->ip ?? 'N/A' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $session->created_at->format('d/m/Y H:i') }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $session->expires_at ? $session->expires_at->format('d/m/Y H:i') : 'N/A' }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>

                        @if($poll->sessions->count() > 10)
                            <div class="mt-4 text-center text-sm text-gray-500">
                                Mostrando 10 de {{ $poll->sessions->count() }} sesiones
                            </div>
                        @endif
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

@if(session('success'))
    <x-notification
        title="Éxito"
        description="{{ session('success') }}"
        icon="success"
    />
@endif

@if(session('error'))
    <x-notification
        title="Error"
        description="{{ session('error') }}"
        icon="error"
    />
@endif

@endsection


@section('script')
    @parent
    <script>
    function copyToClipboard(text) {
        navigator.clipboard.writeText(text).then(function() {
            // Mostrar notificación de éxito
            const notification = document.createElement('div');
            notification.className = 'fixed top-4 right-4 bg-green-500 text-white px-4 py-2 rounded-lg shadow-lg z-50';
            notification.textContent = 'Copiado al portapapeles';
            document.body.appendChild(notification);

            setTimeout(() => {
                notification.remove();
            }, 2000);
        });
    }

    function confirmReset() {
        return confirm('¿Estás seguro de que quieres REINICIAR esta encuesta?\n\nEsta acción eliminará todos los votos y sesiones registradas.\n\nEsta acción NO se puede deshacer.');
    }
    </script>


@endsection
