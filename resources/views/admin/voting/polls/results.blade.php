@extends('layouts.voting')

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <div class="bg-white shadow-sm border-b">
        <div class="container mx-auto px-4 py-6">
            <div class="text-center">
                <h1 class="text-3xl font-bold text-gray-900">Resultados de Votación</h1>
                <p class="text-gray-600 mt-2">Visualiza los resultados de todas las encuestas</p>
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
                        <p class="text-2xl font-bold text-gray-900">{{ $polls->count() }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
                        <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Total Votos</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $polls->sum('votes_count') }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="w-8 h-8 bg-purple-100 rounded-full flex items-center justify-center">
                        <svg class="w-4 h-4 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Activas</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $polls->where('enable', true)->count() }}</p>
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
                        <p class="text-sm font-medium text-gray-600">Promedio Votos</p>
                        <p class="text-2xl font-bold text-gray-900">
                            {{ $polls->count() > 0 ? round($polls->sum('votes_count') / $polls->count()) : 0 }}
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Resultados por encuesta -->
        <div class="space-y-6">
            <h2 class="text-xl font-semibold text-gray-900">Resultados por Encuesta</h2>

            @if($polls->isEmpty())
                <div class="bg-white rounded-lg shadow p-12 text-center">
                    <svg class="w-12 h-12 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">No hay resultados</h3>
                    <p class="text-gray-600">No hay encuestas con votos para mostrar</p>
                </div>
            @else
                @foreach($polls as $poll)
                    <div class="bg-white rounded-lg shadow">
                        <div class="p-6 border-b">
                            <div class="flex justify-between items-start">
                                <div>
                                    <div class="flex items-center gap-2 mb-2">
                                        <h3 class="text-lg font-semibold text-gray-900">{{ $poll->title }}</h3>
                                        <span class="px-2 py-1 text-xs font-medium rounded-full {{ $poll->enable ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                            {{ $poll->enable ? 'Activa' : 'Finalizada' }}
                                        </span>
                                    </div>
                                    <div class="flex items-center gap-4 text-sm text-gray-600">
                                        <span class="flex items-center gap-1">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                            </svg>
                                            {{ $poll->votes_count }} votos totales
                                        </span>
                                        <span>📊 {{ $poll->options->count() }} opciones</span>
                                        @if($poll->created_at)
                                            <span>📅 {{ $poll->created_at->format('d/m/Y') }}</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="p-6">
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
                                        <div class="w-full bg-gray-200 rounded-full h-2">
                                            <div class="bg-blue-600 h-2 rounded-full transition-all duration-300" style="width: {{ $percentage }}%"></div>
                                        </div>
                                    </div>
                                @endforeach

                                @if($poll->votes_count == 0)
                                    <div class="text-center py-8 text-gray-500">
                                        <svg class="w-8 h-8 mx-auto mb-2 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                        </svg>
                                        <p>Aún no hay votos en esta encuesta</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            @endif
        </div>
    </div>
</div>
@endsection
