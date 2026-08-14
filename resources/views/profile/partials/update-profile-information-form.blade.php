<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900 dark:text-white">
            {{ __('Información de Perfil') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600 dark:text-slate-400">
            {{ __("Actualiza la información de tu cuenta y datos personales.") }}
        </p>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" class="mt-6 space-y-6">
        @csrf
        @method('patch')

        <!-- Sección: Usuario (name, email) -->
        <fieldset>
            <legend class="text-sm font-medium text-gray-900 dark:text-white mb-4">Cuenta</legend>

            <div class="space-y-4">
                <div>
                    <x-input-label for="name" :value="__('Nombre')" />
                    <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $user->name)" required autofocus autocomplete="name" />
                    <x-input-error class="mt-2" :messages="$errors->get('name')" />
                </div>

                <div>
                    <x-input-label for="email" :value="__('Email')" />
                    <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" :value="old('email', $user->email)" required autocomplete="username" />
                    <x-input-error class="mt-2" :messages="$errors->get('email')" />

                    @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                        <div>
                            <p class="text-sm mt-2 text-gray-800 dark:text-slate-200">
                                {{ __('Tu dirección de email no está verificada.') }}

                                <button form="send-verification" class="underline text-sm text-gray-600 dark:text-slate-400 hover:text-gray-900 dark:hover:text-white rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 dark:focus:ring-offset-slate-900">
                                    {{ __('Click aquí para reenviar el email de verificación.') }}
                                </button>
                            </p>

                            @if (session('status') === 'verification-link-sent')
                                <p class="mt-2 font-medium text-sm text-green-600 dark:text-green-400">
                                    {{ __('Se ha enviado un nuevo enlace de verificación a tu email.') }}
                                </p>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        </fieldset>

        <!-- Sección: Datos Personales (Profile) -->
        <fieldset>
            <legend class="text-sm font-medium text-gray-900 dark:text-white mb-4 mt-6 pt-4 border-t border-gray-200 dark:border-white/10">Datos Personales</legend>

            <div class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <x-input-label for="firstname" :value="__('Nombre')" />
                        <x-text-input id="firstname" name="firstname" type="text" class="mt-1 block w-full" :value="old('firstname', $user->profile?->firstname)" autocomplete="given-name" />
                        <x-input-error class="mt-2" :messages="$errors->get('firstname')" />
                    </div>

                    <div>
                        <x-input-label for="lastname" :value="__('Apellido')" />
                        <x-text-input id="lastname" name="lastname" type="text" class="mt-1 block w-full" :value="old('lastname', $user->profile?->lastname)" autocomplete="family-name" />
                        <x-input-error class="mt-2" :messages="$errors->get('lastname')" />
                    </div>
                </div>

                <div>
                    <x-input-label for="card_number" :value="__('Número de Cédula/Identidad')" />
                    <x-text-input id="card_number" name="card_number" type="text" class="mt-1 block w-full" :value="old('card_number', $user->profile?->card_number)" autocomplete="off" />
                    <x-input-error class="mt-2" :messages="$errors->get('card_number')" />
                </div>

                <div>
                    <x-input-label for="dir_address" :value="__('Dirección')" />
                    <x-text-input id="dir_address" name="dir_address" type="text" class="mt-1 block w-full" :value="old('dir_address', $user->profile?->dir_address)" autocomplete="street-address" />
                    <x-input-error class="mt-2" :messages="$errors->get('dir_address')" />
                </div>

                <div>
                    <x-input-label for="url_img" :value="__('URL de Imagen de Perfil')" />
                    <x-text-input id="url_img" name="url_img" type="url" class="mt-1 block w-full" :value="old('url_img', $user->profile?->url_img)" placeholder="https://ejemplo.com/imagen.jpg" autocomplete="url" />
                    <x-input-error class="mt-2" :messages="$errors->get('url_img')" />
                    <p class="mt-1 text-xs text-gray-500 dark:text-slate-400">{{ __('Opcional. URL de una imagen para tu avatar.') }}</p>
                </div>
            </div>
        </fieldset>

        <div class="flex items-center gap-3 pt-4 border-t border-gray-200 dark:border-white/10">
            <x-primary-button>{{ __('Guardar Cambios') }}</x-primary-button>

            @if (session('status') === 'profile-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm text-gray-600 dark:text-slate-400"
                >{{ __('Guardado.') }}</p>
            @endif
        </div>
    </form>
</section>