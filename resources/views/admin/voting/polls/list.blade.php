@extends('layouts.voting')

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <div class="bg-white shadow-sm border-b">
        <div class="container mx-auto px-4 py-6">
            <div class="text-center">
                <h1 class="text-3xl font-bold text-gray-900">Encuestas Activas</h1>
                <p class="text-gray-600 mt-2">Participa en las votaciones disponibles</p>
            </div>
        </div>
    </div>

    <div class="container mx-auto px-4 py-8">
        @if($polls->isEmpty())
            <div class="bg-white rounded-lg shadow p-12 text-center max-w-md mx-auto">
                <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                </svg>
                <h2 class="text-xl font-semibold text-gray-900 mb-2">No hay encuestas activas</h2>
                <p class="text-gray-600 mb-4">No hay encuestas disponibles para votar en este momento.</p>
                <a href="{{ url('/') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors">
                    Volver al inicio
                </a>
            </div>
        @else
            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($polls as $poll)
                    <div class="bg-white rounded-lg shadow hover:shadow-lg transition-shadow">
                        <div class="p-6">
                            <div class="flex justify-between items-start mb-2">
                                @if($poll->enable)
                                    <span class="px-2 py-1 bg-green-100 text-green-800 rounded-full text-sm font-medium">
                                        Activa
                                    </span>
                                @else
                                    <span class="px-2 py-1 bg-red-100 text-red-800 rounded-full text-sm font-medium">
                                        Desactiva
                                    </span>
                                @endif

                                @if($poll->time_remaining)
                                    <span class="px-2 py-1 bg-orange-100 text-orange-800 rounded-full text-sm font-medium">
                                        <svg class="w-3 h-3 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        {{ $poll->time_remaining }}
                                    </span>
                                @endif
                            </div>

                            <h3 class="text-lg font-semibold text-gray-900 mb-2">{{ $poll->title }}</h3>

                            <div class="flex items-center gap-4 text-sm text-gray-600 mb-4">
                                <span class="flex items-center gap-1">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                    </svg>
                                    {{ $poll->votes_count }} votos
                                </span>
                                <span class="flex items-center gap-1">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                                    </svg>
                                    {{ $poll->options->count() }} opciones
                                </span>
                            </div>

                            <div class="space-y-3 mb-4">
                                <p class="text-sm font-medium text-gray-700">Opciones:</p>
                                <div class="space-y-1">
                                    @foreach($poll->options->take(3) as $index => $option)
                                        <div class="text-sm text-gray-600 bg-gray-50 px-2 py-1 rounded">
                                            {{ $index + 1 }}. {{ $option->label }}
                                        </div>
                                    @endforeach
                                    @if($poll->options->count() > 3)
                                        <div class="text-sm text-gray-500 italic">
                                            +{{ $poll->options->count() - 3 }} opciones más...
                                        </div>
                                    @endif
                                </div>
                            </div>

                            {{--
                            <a href="{{ route('poll.voting.show', $poll->access_token) }}" class="w-full inline-flex items-center justify-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                                Votar ahora
                                <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                                </svg>
                            </a>
                            --}}
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>
@endsection
