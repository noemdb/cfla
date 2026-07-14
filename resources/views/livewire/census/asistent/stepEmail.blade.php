<div class="max-w-sm mx-auto">
    <h2 class="mb-2 text-2xl font-bold text-white">Paso 2</h2>

    <div class="grid md:grid-cols-1">

        @include('livewire.census.asistent._flashAlert')

        @if (!$verificationCode)

            <p class="mb-4 text-gray-400">Ingresa tu dirección de correo electrónico</p>
            <div class="mb-2 space-y-2">
                <x-input wire:model="email" label="Correo Electrónico" placeholder="Tu email"
                    right-icon="at-symbol" description="Inform your full name" />
            </div>

            <div class="mb-2 space-y-2">
                <x-button wire:click="sendEmailCode" xl positive label="Conecta tu email con nosotros"
                    class="w-full !bg-green-800 hover:!bg-green-900 border-2 !border-green-900 shadow-lg" type="button" />
            </div>

        @else

            <p class="mb-4 text-gray-400">Se ha enviado un código a tu correo electrónico</p>

            <x-card>
                <p class="mb-4 font-extrabold text-gray-400">
                    Tu dirección de correo electrónico es:
                    <span class="font-normal">{{ $email ?? null }}</span>
                </p>
            </x-card>

            <div class="py-2 my-2"></div>

            <div class="mb-2 space-y-2">
                <x-input
                    wire:model="input_code"
                    label="Ingresa el código que has recibido en tu dirección de correo electrónico"
                    placeholder="Código"
                    {{-- icon="hashtag"  --}}
                    suffix="#"
                />
            </div>

            <div class="mb-2 space-y-2">
                <x-button
                    wire:click="validateEmailCode"
                    xl
                    positive
                    label="Valida tu dirección de correo electrónico"
                    class="w-full !bg-green-800 hover:!bg-green-900 border-2 !border-green-900 shadow-lg"
                />
            </div>

        @endif

    </div>

</div>
