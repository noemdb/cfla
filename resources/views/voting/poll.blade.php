@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="container mx-auto px-4">
        <div class="max-w-2xl mx-auto">
            @if(!$poll->enable)
                <div class="bg-white rounded-lg shadow p-8 text-center">
                    <svg class="w-16 h-16 text-orange-500 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <h2 class="text-xl font-semibold mb-2">Encuesta no disponible</h2>
                    <p class="text-gray-600">Esta encuesta no está activa en este momento.</p>
                </div>
            @elseif($hasVoted)
                <div class="bg-white rounded-lg shadow p-8 text-center">
                    <svg class="w-16 h-16 text-green-500 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <h2 class="text-xl font-semibold mb-2">¡Gracias por votar!</h2>
                    <p class="text-gray-600 mb-4">Tu voto ha sido registrado correctamente.</p>
                    <span class="px-3 py-1 bg-green-100 text-green-800 rounded-full text-sm font-medium">
                        Voto confirmado
                    </span>
                </div>
            @else
                <div class="bg-white rounded-lg shadow">
                    <div class="p-6 text-center border-b">
                        <div class="flex justify-center items-center gap-2 mb-2">
                            <span class="px-2 py-1 bg-green-100 text-green-800 rounded-full text-sm font-medium">
                                Activa
                            </span>
                            @if($poll->time_remaining)
                                <span class="px-2 py-1 bg-orange-100 text-orange-800 rounded-full text-sm font-medium">
                                    <svg class="w-3 h-3 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    {{ $poll->time_remaining }}
                                </span>
                            @endif
                        </div>
                        <h1 class="text-2xl font-bold text-gray-900">{{ $poll->title }}</h1>
                        <p class="text-gray-600 mt-2">Selecciona una opción y haz clic en "Votar"</p>
                    </div>

                    <form action="{{ route('poll.voting.vote', $poll->access_token) }}" method="POST" class="p-6">
                        @csrf
                        <div class="space-y-3 mb-6">
                            @foreach($poll->options as $option)
                                <label class="flex items-center p-4 border rounded-lg hover:bg-gray-50 cursor-pointer transition-colors">
                                    <input 
                                        type="radio" 
                                        name="option_id" 
                                        value="{{ $option->id }}" 
                                        class="w-4 h-4 text-blue-600 border-gray-300 focus:ring-blue-500"
                                        required
                                    >
                                    <span class="ml-3 text-base text-gray-900">{{ $option->label }}</span>
                                </label>
                            @endforeach
                        </div>

                        <button type="submit" class="w-full px-4 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors font-medium text-lg">
                            Votar
                        </button>

                        <div class="mt-6 text-center text-sm text-gray-500">
                            <p>🔒 Tu voto es anónimo y seguro</p>
                            <p>Solo puedes votar una vez en esta encuesta</p>
                        </div>
                    </form>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Script para fingerprinting -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Generar fingerprint simple
    const fingerprint = btoa(JSON.stringify({
        userAgent: navigator.userAgent,
        language: navigator.language,
        platform: navigator.platform,
        screen: screen.width + 'x' + screen.height,
        timezone: Intl.DateTimeFormat().resolvedOptions().timeZone
    })).slice(0, 32);

    // Enviar fingerprint al servidor
    fetch('{{ route("voting.store-fingerprint") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            fingerprint: fingerprint,
            poll_token: '{{ $poll->access_token }}'
        })
    }).catch(error => {
        console.error('Error storing fingerprint:', error);
    });
});
</script>

@if(session('success'))
    <x-notification 
        title="Éxito" 
        description="{{ session('success') }}" 
        icon="success" 
    />
@endif

@if(session('error'))
    <x-notification 
        title="Error" 
        description="{{ session('error') }}" 
        icon="error" 
    />
@endif
@endsection
