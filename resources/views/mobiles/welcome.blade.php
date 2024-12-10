@extends('layouts.app')

@section('content')
<div class="flex flex-col items-center justify-center min-h-screen bg-gradient-to-b from-blue-500 to-blue-800 text-white">
    <!-- Hero Section -->
    <div class="fade-in text-center px-6">
        <h1 class="text-4xl font-bold md:text-6xl">¡Bienvenido a Innovative App!</h1>
        <p class="mt-4 text-lg md:text-xl">
            La plataforma diseñada para transformar la forma en que interactúas con la tecnología.
        </p>
    </div>

    <!-- Features Section -->
    <div class="fade-in mt-12 grid grid-cols-1 md:grid-cols-3 gap-6 px-6 max-w-4xl">
        <div class="bg-white bg-opacity-10 rounded-lg p-6 text-center shadow-lg">
            <h3 class="text-xl font-semibold">Innovación</h3>
            <p class="mt-2 text-sm">
                Tecnología de punta al alcance de tus manos.
            </p>
        </div>
        <div class="bg-white bg-opacity-10 rounded-lg p-6 text-center shadow-lg">
            <h3 class="text-xl font-semibold">Accesibilidad</h3>
            <p class="mt-2 text-sm">
                Diseñado pensando en la experiencia móvil.
            </p>
        </div>
        <div class="bg-white bg-opacity-10 rounded-lg p-6 text-center shadow-lg">
            <h3 class="text-xl font-semibold">Seguridad</h3>
            <p class="mt-2 text-sm">
                Protegemos tus datos con tecnología avanzada.
            </p>
        </div>
    </div>

    <!-- Call to Action -->
    <div class="fade-in mt-12 text-center">
        <a href="{{ route('login') }}" class="inline-block px-6 py-3 text-lg font-medium bg-white text-blue-800 rounded-lg shadow-lg hover:bg-blue-100 transition">
            Comienza ahora
        </a>
    </div>
</div>
@endsection
