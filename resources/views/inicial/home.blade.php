{{--
    Portada del módulo de Educación Inicial (docente).

    Controlador: App\Http\Controllers\Inicial\HomeInicialController::index
    Ruta:        inicials.home → /app/inicials

    ─────────────────────────────────────────────────────────────────────────────
    DISEÑO SIGUE EL PATRÓN DE /app/profesors/home
    ─────────────────────────────────────────────────────────────────────────────
    El módulo usa el layout REAL (<x-layouts.role>) a través de
    `profesors.layouts.app`, no el esqueleto de Jetstream: así se cargan
    Livewire/WireUI y la navbar-info con el docente y el lapso activo.

    La portada replica la estructura del dashboard del profesor:

      · header con saludo + subtítulo;
      · una TARJETA POR DOCUMENTO, cada una con el borde superior del color de su
        grupo (como los indicadores del dashboard del profesor);
      · contadores de contexto (documentos, proyectos, evaluaciones cerradas).

    El legacy resolvía esto con una navbar lateral de 86 líneas y una tarjeta de
    perfil; aquí cada documento es una tarjeta con su enlace, que es el mismo
    criterio de "sin menús propios" del resto del proyecto.
--}}
@extends('profesors.layouts.app')

@section('title', 'Educación Inicial - ' . config('app.name', 'SAEFL'))

@section('navbar-info')
    @if ($esDocente && $profesor)
        <div class="hidden lg:flex items-center gap-3 ml-6 px-2 py-1 bg-gray-900/30 backdrop-blur-md rounded-lg">
            <div class="flex items-center gap-1.5 text-xs text-gray-400">
                <svg class="w-3.5 h-3.5 text-emerald-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                </svg>
                <span class="text-white font-medium">{{ $profesor->name ?? '—' }}</span>
            </div>

            <span class="w-px h-4 bg-white/5"></span>

            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[10px] font-bold bg-cyan-500/10 text-cyan-300 border border-cyan-500/20">
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5zm6-2.5V15a2 2 0 01-2 2H8a2 2 0 01-2-2v-3.5m12-1V6m-10 4V8.5m3 4.5v3m4-3v3" />
                </svg>
                Educación Inicial
            </span>

            <span class="w-px h-4 bg-white/5"></span>

            @if ($lapsoActivo)
                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[10px] font-bold bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span>
                    {{ $lapsoActivo->name }}
                </span>
            @endif
        </div>
    @endif
@endsection

@section('content')
<div class="fade-in">

    {{-- ═══════════════════════════════════════════════════════════════════
         HEADER
         ═══════════════════════════════════════════════════════════════════ --}}
    <div class="mb-6 sm:mb-8 flex flex-col md:flex-row md:items-center justify-between gap-3">
        <div>
            <h1 class="text-lg font-extrabold text-white mb-2">
                {{ $esDocente && $profesor ? 'Bienvenido, '.$profesor->full_name : 'Educación Inicial' }}
            </h1>
            <p class="text-cyan-400 font-medium">Formatos para la planificación y la evaluación</p>
        </div>

        <div class="flex items-center gap-3">
            <a href="{{ route('inicials.use-cases') }}"
                class="inline-flex items-center gap-2 px-4 py-2 bg-white/5 hover:bg-white/10 text-gray-400 hover:text-cyan-300 rounded-lg border border-white/5 transition-all duration-300 text-xs font-bold">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                <span class="hidden sm:inline">Casos de uso</span>
            </a>

            @if ($esDocente && $profesor)
                <a href="{{ route('app.profesors.users.index') }}"
                    class="inline-flex items-center gap-2 px-4 py-2 bg-white/5 hover:bg-white/10 text-gray-400 hover:text-emerald-300 rounded-lg border border-white/5 transition-all duration-300 text-xs font-bold">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                    <span class="hidden sm:inline">Mi Perfil</span>
                </a>
            @endif
        </div>
    </div>

    {{-- ═══════════════════════════════════════════════════════════════════
         DOCUMENTOS (tarjetas con borde de color, como el dashboard)
         ═══════════════════════════════════════════════════════════════════ --}}
    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
        @foreach ($documentos as $documento)
            @php
                $borde = [
                    'cyan' => 'border-t-cyan-500',
                    'sky' => 'border-t-sky-500',
                    'indigo' => 'border-t-indigo-500',
                    'rose' => 'border-t-rose-500',
                    'amber' => 'border-t-amber-500',
                    'emerald' => 'border-t-emerald-500',
                    'gray' => 'border-t-gray-500',
                ][$documento['color']] ?? 'border-t-gray-500';
            @endphp

            <a href="{{ $documento['ruta'] }}"
                class="group rounded-xl border border-white/10 border-t-4 {{ $borde }} bg-white/5 hover:bg-white/10 p-5 transition-all duration-300 hover:-translate-y-1 hover:shadow-lg hover:shadow-emerald-950/30">
                <div class="flex items-start justify-between gap-3">
                    <div class="min-w-0">
                        <p class="text-sm font-bold text-gray-100 group-hover:text-white transition-colors">
                            {{ $documento['titulo'] }}
                        </p>
                        <p class="mt-1 text-xs text-gray-400">
                            {{ number_format($stats[$documento['clave']] ?? 0) }} registro(s)
                        </p>
                    </div>

                    <svg class="w-4 h-4 shrink-0 text-gray-600 group-hover:text-cyan-300 transition-colors"
                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </div>
            </a>
        @endforeach
    </div>

    {{-- ═══════════════════════════════════════════════════════════════════
         CONTEXTO DEL TRABAJO
         ═══════════════════════════════════════════════════════════════════ --}}
    <div class="mt-6 grid gap-4 sm:grid-cols-3">
        <div class="rounded-xl border border-white/10 bg-white/5 p-5">
            <p class="text-xs text-gray-400">Documentos registrados</p>
            <p class="mt-1 text-3xl font-bold tabular-nums text-white">{{ number_format($stats['totalRecords']) }}</p>
        </div>
        <div class="rounded-xl border border-white/10 bg-white/5 p-5">
            <p class="text-xs text-gray-400">Proyectos en curso</p>
            <p class="mt-1 text-3xl font-bold tabular-nums text-white">{{ number_format($stats['activeProjects']) }}</p>
        </div>
        <div class="rounded-xl border border-white/10 bg-white/5 p-5">
            <p class="text-xs text-gray-400">Evaluaciones cerradas</p>
            <p class="mt-1 text-3xl font-bold tabular-nums text-white">{{ number_format($stats['completedEvaluations']) }}</p>
        </div>
    </div>

    @if ($esDocente)
        <p class="mt-4 text-xs text-gray-500">
            Si ve ceros, es que aún no ha creado ninguno: las áreas de aprendizaje del módulo
            ya están sembradas y son el prerrequisito del informe final.
        </p>
    @else
        <p class="mt-4 text-xs text-gray-500">
            Vista de administración: los totales incluyen lo escrito por todo el personal de
            Educación Inicial.
        </p>
    @endif
</div>
@endsection