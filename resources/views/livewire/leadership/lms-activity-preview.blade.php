@extends('leadership.layouts.app')

@section('content')
    <div class="max-w-6xl mx-auto py-6 px-4 sm:px-6 space-y-6">
        {{-- ── Navegación superior ── --}}
        <div class="flex items-center justify-between">
            <a href="{{ url()->previous() }}"
               class="group inline-flex items-center gap-1.5 text-xs font-medium text-slate-500 hover:text-emerald-400 transition-colors duration-200">
                <svg class="w-4 h-4 transition-transform duration-200 group-hover:-translate-x-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                Volver
            </a>
        </div>

        {{-- ── Componente reutilizable de vista previa ── --}}
        <x-lms-activity-preview :activity="$activity" />
    </div>
@endsection
