@extends('profesors.layouts.app')

@section('title', 'Mi Perfil - ' . config('app.name', 'SAEFL'))

@section('content')
<div class="fade-in">

    {{-- Header --}}
    <div class="mb-8 flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-white mb-2">Mi Perfil</h1>
            <p class="text-emerald-400 font-medium">Información personal del profesor</p>
        </div>
        <a href="{{ route('app.profesors.home') }}"
            class="inline-flex items-center gap-2 px-5 py-2.5 bg-cyan-500/10 hover:bg-cyan-500/20 text-cyan-400 rounded-xl border border-cyan-500/20 transition-all duration-300 text-sm font-bold">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            Volver al Dashboard
        </a>
    </div>

    @if(isset($profesor) && $profesor)
    <div class="bg-gray-900/40 backdrop-blur-md border border-white/5 rounded-2xl overflow-hidden">
        {{-- Profile header --}}
        <div class="px-6 py-5 border-b border-white/5 flex items-center gap-4">
            <div class="w-14 h-14 rounded-full bg-emerald-500/10 flex items-center justify-center">
                <svg class="w-7 h-7 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                </svg>
            </div>
            <div>
                <h2 class="text-xl font-bold text-white">{{ $profesor->full_name }}</h2>
                <p class="text-sm text-gray-400">{{ $profesor->email ?? 'Sin correo registrado' }}</p>
            </div>
        </div>

        {{-- Fields grid --}}
        <div class="p-6">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                @php
                    $fields = [
                        'Nombre Completo'  => ['value' => $profesor->full_name, 'color' => 'emerald'],
                        'Cédula'           => ['value' => $profesor->ci_profesor, 'color' => 'blue'],
                        'Correo'           => ['value' => $profesor->email ?? '—', 'color' => 'purple'],
                        'Teléfono'         => ['value' => $profesor->phone ?? '—', 'color' => 'amber'],
                        'Celular'          => ['value' => $profesor->cellphone ?? '—', 'color' => 'cyan'],
                        'Género'           => ['value' => $profesor->gender ?? '—', 'color' => 'pink'],
                        'Tipo Facilitador' => ['value' => $profesor->ti_teacher ?? '—', 'color' => 'indigo'],
                        'F. Nacimiento'    => ['value' => $profesor->date_birth ? \Carbon\Carbon::parse($profesor->date_birth)->format('d/m/Y') : '—', 'color' => 'teal'],
                    ];
                @endphp

                @foreach($fields as $label => $data)
                <div class="bg-white/5 rounded-xl px-4 py-3 border border-white/5">
                    <p class="text-[10px] font-bold uppercase tracking-widest text-gray-500 mb-1">{{ $label }}</p>
                    <p class="text-sm font-medium text-white">{{ $data['value'] }}</p>
                </div>
                @endforeach

                {{-- Estado --}}
                <div class="bg-white/5 rounded-xl px-4 py-3 border border-white/5">
                    <p class="text-[10px] font-bold uppercase tracking-widest text-gray-500 mb-1">Estado</p>
                    @php $isActive = $profesor->status_active === 'true'; @endphp
                    <span class="inline-flex items-center gap-1 text-[10px] font-bold uppercase tracking-wider {{ $isActive ? 'text-emerald-400 bg-emerald-500/10' : 'text-red-400 bg-red-500/10' }} px-2.5 py-1 rounded-full">
                        <span class="w-1.5 h-1.5 rounded-full {{ $isActive ? 'bg-emerald-400' : 'bg-red-400' }}"></span>
                        {{ $isActive ? 'Activo' : 'Inactivo' }}
                    </span>
                </div>
            </div>
        </div>
    </div>
    @else
    {{-- Empty State --}}
    <div class="bg-gray-900/40 backdrop-blur-md border border-white/5 rounded-2xl p-12 text-center">
        <svg class="w-16 h-16 text-gray-700 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
        </svg>
        <p class="text-lg font-medium text-gray-400">Información no disponible</p>
        <p class="text-sm text-gray-600 mt-1">No se encontraron datos del profesor asociados a tu cuenta.</p>
    </div>
    @endif
</div>
@endsection
