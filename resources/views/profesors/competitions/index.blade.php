@extends('profesors.layouts.app')

@section('title', 'Competencias - ' . config('app.name'))

@section('content')
    <div class="w-full px-2 mx-auto">
        {{-- Header --}}
        <div class="flex items-center gap-3 mb-6">
            <div class="w-12 h-12 rounded-lg bg-gradient-to-br from-emerald-500 to-emerald-700 flex items-center justify-center shadow-lg shadow-emerald-500/20">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                </svg>
            </div>
            <div>
                <h1 class="text-lg font-bold text-white">Competencias Académicas</h1>
                <p class="text-sm text-gray-500">Gestión de bancos de preguntas para debates educativos</p>
            </div>
            <div class="ml-auto text-right">
                <span class="text-xs text-gray-500">Prof:</span>
                <span class="text-sm font-medium text-emerald-400">{{ $profesor->full_name ?? '—' }}</span>
            </div>
        </div>

        {{-- Livewire Component --}}
        @livewire('profesor.competition.index-component')
    </div>
@endsection
