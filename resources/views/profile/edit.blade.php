@extends('layouts.dashboard')

@section('content')
    <div class="w-full mx-auto max-w-3xl space-y-6">
        <div>
            <h1 class="text-lg font-bold text-gray-900 dark:text-white">Perfil de Usuario</h1>
            <p class="text-sm text-gray-500 dark:text-slate-400 mt-1">
                Gestiona la información de tu cuenta, tus datos personales y tu seguridad.
            </p>
        </div>

        <div class="bg-white dark:bg-slate-900 border border-gray-200 dark:border-white/5 rounded-xl overflow-hidden shadow-sm">
            <div class="p-4 sm:p-6">
                @include('profile.partials.update-profile-information-form')
            </div>
        </div>

        <div class="bg-white dark:bg-slate-900 border border-gray-200 dark:border-white/5 rounded-xl overflow-hidden shadow-sm">
            <div class="p-4 sm:p-6">
                @include('profile.partials.update-password-form')
            </div>
        </div>

        <div class="bg-white dark:bg-slate-900 border border-gray-200 dark:border-white/5 rounded-xl overflow-hidden shadow-sm">
            <div class="p-4 sm:p-6">
                @include('profile.partials.delete-user-form')
            </div>
        </div>
    </div>
@endsection
