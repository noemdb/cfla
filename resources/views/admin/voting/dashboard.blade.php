@extends('layouts.voting')

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <div class="bg-white shadow-sm border-b">
        <div class="container mx-auto px-4 py-6">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Panel Administrativo</h1>
                    <p class="text-gray-600 mt-1">Gestiona tus encuestas y votaciones</p>
                </div>
                <a href="{{ route('admin.voting.polls.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    Nueva Encuesta
                </a>
            </div>
        </div>
    </div>

    <div class="container mx-auto px-4 py-8">
        <!-- Estadísticas generales -->
        <div class="grid md:grid-cols-4 gap-6 mb-8">
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                        <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Total Encuestas</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $stats['total_polls'] }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
                        <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h1m4 0h1m-6 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Activas</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $stats['active_polls'] }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="w-8 h-8 bg-purple-100 rounded-full flex items-center justify-center">
                        <svg class="w-4 h-4 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Total Votos</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $stats['total_votes'] }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="w-8 h-8 bg-orange-100 rounded-full flex items-center justify-center">
                        <svg class="w-4 h-4 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Finalizadas</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $stats['finished_polls'] }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Lista de encuestas -->
        <div class="space-y-4">
            <h2 class="text-xl font-semibold text-gray-900">Encuestas</h2>

            @if($polls->isEmpty())
                <div class="bg-white rounded-lg shadow p-12 text-center">
                    <svg class="w-12 h-12 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">No hay encuestas</h3>
                    <p class="text-gray-600 mb-4">Crea tu primera encuesta para comenzar</p>
                    <a href="{{ route('admin.voting.polls.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        Crear Encuesta
                    </a>
                </div>
            @else
                @foreach($polls as $poll)
                    <div class="bg-white rounded-lg shadow hover:shadow-md transition-shadow">
                        <div class="p-6">
                            <div class="flex justify-between items-start">
                                <div class="flex-1">
                                    <div class="flex items-center gap-2 mb-2">
                                        <h3 class="text-lg font-semibold text-gray-900">{{ $poll->title }}</h3>
                                        <span class="px-2 py-1 text-xs font-medium rounded-full {{ $poll->enable ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                            {{ $poll->enable ? 'Activa' : 'Inactiva' }}
                                        </span>
                                    </div>
                                    <div class="flex flex-wrap gap-4 text-sm text-gray-600 mb-2">
                                        <span>🔗 Token: {{ $poll->access_token }}</span>
                                        <span>⏱️ Duración: {{ $poll->time_active }} minutos</span>
                                        <span>📊 Opciones: {{ $poll->options->count() }}</span>
                                        <span>🗳️ Votos: {{ $poll->votes_count }}</span>
                                    </div>
                                    @if($poll->enable && $poll->time_remaining)
                                        <div class="mt-2">
                                            <span class="px-2 py-1 text-xs font-medium bg-orange-100 text-orange-800 rounded-full">
                                                {{ $poll->time_remaining }}
                                            </span>
                                        </div>
                                    @endif
                                </div>
                                <div class="flex gap-2 ml-4">
                                    @if($poll->enable)
                                        <form action="{{ route('admin.voting.polls.stop', $poll) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit" class="px-3 py-1 text-sm bg-red-600 text-white rounded hover:bg-red-700 transition-colors">
                                                ⏹️ Detener
                                            </button>
                                        </form>
                                    @else
                                        <form action="{{ route('admin.voting.polls.start', $poll) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit" class="px-3 py-1 text-sm bg-green-600 text-white rounded hover:bg-green-700 transition-colors">
                                                ▶️ Iniciar
                                            </button>
                                        </form>
                                    @endif
                                    <a href="{{ route('admin.voting.polls.edit', $poll) }}" class="px-3 py-1 text-sm bg-blue-600 text-white rounded hover:bg-blue-700 transition-colors">
                                        ✏️ Editar
                                    </a>
                                    <form action="{{ route('admin.voting.polls.destroy', $poll) }}" method="POST" class="inline" onsubmit="return confirm('¿Estás seguro?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="px-3 py-1 text-sm bg-gray-600 text-white rounded hover:bg-gray-700 transition-colors">
                                            🗑️ Eliminar
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                        @if($poll->enable)
                            <div class="px-6 pb-6">
                                <div class="bg-blue-50 p-3 rounded-lg">
                                    <p class="text-sm text-blue-800 font-medium">
                                        🔗 Enlace público:
                                        <code class="ml-2 bg-white px-2 py-1 rounded text-xs">
                                            {{ url('/poll/voting/' . $poll->access_token) }}
                                        </code>
                                    </p>
                                </div>
                            </div>
                        @endif
                    </div>
                @endforeach
            @endif
        </div>
    </div>
</div>

{{-- @if(session('success'))
    <x-notification
        title="Éxito"
        description="{{ session('success') }}"
        icon="success"
    />
@endif --}}
@endsection
