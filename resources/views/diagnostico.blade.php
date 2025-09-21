@extends('layouts.diagnostic')

@section('title', 'Diagnóstico Académico')

@section('content')
    <div class="py-4 bg-gray-900">
        @livewire('diagnostic')
    </div>
@endsection

@push('styles')
    <style>
        :root {
            --primary-green: #064e3b;
            --secondary-green: #065f46;
            --accent-green: #10b981;
            --dark-bg: #111827;
            --card-bg: #1f2937;
        }

        .diagnostic-card {
            transition: all 0.3s ease;
            background: linear-gradient(135deg, var(--card-bg) 0%, var(--primary-green) 100%);
        }

        .diagnostic-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.3);
        }

        .progress-ring {
            transition: stroke-dasharray 0.5s ease-in-out;
        }
    </style>
@endpush
