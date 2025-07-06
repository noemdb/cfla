@extends('layouts.vote')

@section('title', 'Resultados - ' . $poll->title)

@section('content')
    <div class="min-h-screen bg-gradient-to-br from-green-950 via-green-900 to-green-950">
        <!-- Header con información de la encuesta -->
        <div class="bg-gradient-to-r from-green-900/80 to-green-800/80 backdrop-blur-sm border-b border-green-700/30">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
                <div class="text-center">
                    <div
                        class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-green-600 to-green-700 rounded-full text-white text-sm font-medium mb-4">
                        <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                clip-rule="evenodd"></path>
                        </svg>
                        Encuesta Activa
                    </div>
                    <h1 class="text-4xl font-bold text-white mb-4">{{ $poll->title }}</h1>
                    @if ($poll->description)
                        <p class="text-green-100 text-lg max-w-3xl mx-auto">{{ $poll->description }}</p>
                    @endif
                </div>
            </div>
        </div>

        <!-- Componente Livewire para resultados en tiempo real -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            @livewire('voting-poll-result', ['access_token' => $access_token])
        </div>

        <!-- Footer con información adicional -->
        <div class="bg-gradient-to-r from-green-900/50 to-green-800/50 backdrop-blur-sm border-t border-green-700/30 mt-16">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
                <div class="text-center">
                    <p class="text-green-200 text-sm">
                        Los resultados se actualizan automáticamente cada 3 segundos
                    </p>
                    <div class="mt-4 flex justify-center space-x-4">
                        <a href="{{ route('poll.voting.index') }}"
                            class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-green-700 to-green-800 hover:from-green-600 hover:to-green-700 text-white rounded-lg transition-all duration-300 transform hover:scale-105">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                            </svg>
                            Volver a Encuestas
                        </a>
                        <button onclick="window.print()"
                            class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-green-600 to-green-700 hover:from-green-500 hover:to-green-600 text-white rounded-lg transition-all duration-300 transform hover:scale-105">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z">
                                </path>
                            </svg>
                            Imprimir Resultados
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>


@endsection

@section('content')
@parent
<style>
        @media print {
            .no-print {
                display: none !important;
            }

            body {
                background: white !important;
            }

            .bg-gradient-to-br {
                background: white !important;
            }

            .text-white {
                color: black !important;
            }

            .text-green-100 {
                color: #333 !important;
            }

            .text-green-200 {
                color: #666 !important;
            }
        }
    </style>
@endsection
