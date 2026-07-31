{{-- Título de página unificado: env('APP_NAME') - rol - nombre completo del usuario --}}
@php($user = auth()->user())
{{ env('APP_NAME') }}{{ $user ? ' - ' . $user->rol . ' - ' . $user->profile?->fullname : '' }}