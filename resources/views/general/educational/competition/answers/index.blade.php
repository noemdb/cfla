@extends('layouts.dashboard')

@section('content')
    @php
        $competition = \App\Models\app\Educational\DebateCompetition::where('token', $token)->first();
    @endphp
    <div class="px-4 py-8 mx-auto w-full sm:px-6 lg:px-8 fade-in">
        <div
            class="mb-8 flex flex-col md:flex-row md:items-center justify-between bg-gray-900/40 backdrop-blur-md p-6 rounded-3xl border border-white/5 shadow-xl shadow-blue-500/5">
            <div class="mb-4 md:mb-0">
                <h1 class="text-3xl font-extrabold text-white mb-2 tracking-tight">Auditoría de Respuestas</h1>
                <p class="text-blue-400 font-medium">Revisión de respuestas emitidas para: <span
                        class="text-emerald-300">"{{ $competition->name }}"</span></p>
            </div>
            <a href="{{ route('admin.educational.competition.index') }}"
                class="flex items-center justify-center gap-2 px-5 py-2.5 bg-white/5 hover:bg-white/10 text-gray-300 rounded-xl border border-white/5 transition-all text-sm font-bold">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18">
                    </path>
                </svg>
                Panel General
            </a>
        </div>

        <!-- Livewire Component Injection -->
        <livewire:app.general.educational.competition.answers-component :competition="$competition" />
    </div>
@endsection
