@extends('layouts.auth')

@section('title', 'Iniciar Sesión - ' . config('app.name'))

@section('content')
<div class="flex items-center justify-center min-h-[calc(100vh-200px)] px-4">
    <div class="w-full max-w-md">
        <div class="diagnostic-card bg-gray-900/40 backdrop-blur-md border border-emerald-500/20 rounded-2xl shadow-2xl overflow-hidden p-8 fade-in">
            <div class="mb-8 text-center text-white">
                <div class="inline-flex items-center justify-center w-20 h-20 bg-emerald-500/10 rounded-full mb-4 border border-emerald-500/20">
                    <svg class="w-10 h-10 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2H6a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                </div>
                <h2 class="text-3xl font-extrabold mb-2">Acceso al Sistema</h2>
                <p class="text-emerald-400">Ingresa tus credenciales para continuar</p>
            </div>

            <form method="POST" action="{{ route('login') }}" class="space-y-6">
                @csrf

                @if ($errors->any())
                    <div class="bg-red-500/10 border border-red-500/50 text-red-400 p-3 rounded-xl text-sm animate-pulse text-center">
                        @foreach ($errors->all() as $error)
                            <p>{{ $error }}</p>
                        @endforeach
                    </div>
                @endif

                <div class="space-y-2">
                    <label for="username" class="block text-sm font-medium text-emerald-300 ml-1">Usuario</label>
                    <div class="relative group">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none transition-colors group-focus-within:text-emerald-500 text-gray-400">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                        </div>
                        <input type="text" id="username" name="username" value="{{ old('username') }}" required autofocus
                            class="w-full bg-gray-800/50 border border-emerald-800/30 text-white pl-10 pr-4 py-3 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition-all duration-300 placeholder-gray-500 shadow-inner"
                            placeholder="Tu nombre de usuario">
                    </div>
                </div>

                <div class="space-y-2">
                    <label for="password" class="block text-sm font-medium text-emerald-300 ml-1">Contraseña</label>
                    <div class="relative group">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none transition-colors group-focus-within:text-emerald-500 text-gray-400">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2H6a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                            </svg>
                        </div>
                        <input type="password" id="password" name="password" required
                            class="w-full bg-gray-800/50 border border-emerald-800/30 text-white pl-10 pr-4 py-3 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition-all duration-300 placeholder-gray-500 shadow-inner"
                            placeholder="••••••••">
                    </div>
                </div>

                <button type="submit" class="w-full btn-diagnostic text-white font-bold py-4 rounded-xl shadow-lg transform transition-all duration-300 hover:scale-[1.02] active:scale-[0.98] flex items-center justify-center space-x-2">
                    <span>Entrar</span>
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                    </svg>
                </button>
            </form>
        </div>
    </div>
</div>
@endsection


