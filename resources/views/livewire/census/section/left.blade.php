<div class="h-full w-full overflow-hidden rounded-[40px] bg-gradient-to-b from-green-400 via-green-600 to-black">
    <div class="flex h-full flex-col items-center justify-center px-4 text-center text-white">
        <h2 class="mb-2 text-4xl font-extrabold uppercase mt-4">U.E. Colegio Fray Luis Amigó</h2>
        <div class="mb-1">
            <h1 class="text-3xl font-semibold">Censo Escolar 26-27 - Asistente</h1>
        </div>
        <div class="text-xl">El primer paso hacia una educación de excelencia.</div>
        <div class="mb-8 font-semibold text-3xl rounded-lg bg-white/10 p-4 backdrop-blur-sm">Primera convocatoria: Desde
            el 28 hasta 30 de abril, a las 2pm.</div>

        <div class="w-full max-full space-y-4">
            {{-- Paso 1 --}}
            <div class="rounded-lg {{ $currentStep == 1 ? 'bg-white/10' : 'bg-white/5' }} p-4 backdrop-blur-sm">
                <div class="flex items-center gap-4">
                    <span
                        class="flex h-8 w-8 items-center justify-center rounded-full {{ $currentStep == 1 ? 'bg-white text-black' : 'bg-white/20 text-white' }}">1</span>
                    <span class="text-lg">Consulta de Cédula</span>
                </div>
            </div>

            {{-- Paso 2 --}}
            <div class="rounded-lg {{ $currentStep == 2 ? 'bg-white/10' : 'bg-white/5' }} p-4 backdrop-blur-sm">
                <div class="flex items-center gap-4">
                    <span
                        class="flex h-8 w-8 items-center justify-center rounded-full {{ $currentStep == 2 ? 'bg-white text-black' : 'bg-white/20 text-white' }}">
                        2
                    </span>
                    <span class="text-lg">
                        @if ($wizardFlow === 'A')
                            Validar Email
                        @elseif($wizardFlow === 'B')
                            Historial de Censo
                        @else
                            Verificación
                        @endif
                    </span>
                </div>
            </div>

            {{-- Paso 3 --}}
            <div class="rounded-lg {{ $currentStep == 3 ? 'bg-white/10' : 'bg-white/5' }} p-4 backdrop-blur-sm">
                <div class="flex items-center gap-4">
                    <span
                        class="flex h-8 w-8 items-center justify-center rounded-full {{ $currentStep == 3 ? 'bg-white text-black' : 'bg-white/20 text-white' }}">
                        3
                    </span>
                    <span class="text-lg">Datos del Estudiante</span>
                </div>
            </div>

            {{-- Paso 4 --}}
            <div class="rounded-lg {{ $currentStep == 4 ? 'bg-white/10' : 'bg-white/5' }} p-4 backdrop-blur-sm">
                <div class="flex items-center gap-4">
                    <span
                        class="flex h-8 w-8 items-center justify-center rounded-full {{ $currentStep == 4 ? 'bg-white text-black' : 'bg-white/20 text-white' }}">
                        4
                    </span>
                    <span class="text-lg">Datos del Representante</span>
                </div>
            </div>

            {{-- Paso 5 --}}
            <div class="rounded-lg {{ $currentStep == 5 ? 'bg-white/10' : 'bg-white/5' }} p-4 backdrop-blur-sm">
                <div class="flex items-center gap-4">
                    <span
                        class="flex h-8 w-8 items-center justify-center rounded-full {{ $currentStep == 5 ? 'bg-white text-black' : 'bg-white/20 text-white' }}">
                        5
                    </span>
                    <span class="text-lg">Planilla de Registro</span>
                </div>
            </div>

            {{-- Código de Vestimenta --}}
            <div class="rounded-lg bg-white/5 p-4 backdrop-blur-sm hover:bg-white/10 transition">
                <div class="flex items-center gap-4 cursor-pointer" onclick="$openModal('dress-code-modal')">
                    <span class="flex h-8 w-8 items-center justify-center rounded-full bg-white/20 text-white">
                        <x-icon name="information-circle" class="w-5 h-5" />
                    </span>
                    <span class="text-lg font-semibold">Código de Vestimenta</span>
                </div>
            </div>

            {{-- Botón Reset --}}
            <div
                class="rounded-lg {{ $currentStep == 5 ? 'bg-white/10' : 'bg-white/5' }} p-4 backdrop-blur-sm hover:bg-white/10 transition">
                <div class="flex items-center gap-4 cursor-pointer" wire:click="restart">
                    <span
                        class="flex h-8 w-8 items-center justify-center rounded-full {{ $currentStep == 5 ? 'bg-white text-black' : 'bg-white/20 text-white' }}">
                        <x-icon name="arrow-path" class="w-5 h-5" />
                    </span>
                    <span class="text-lg font-semibold">Empezar de nuevo</span>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal Código de Vestimenta --}}
    <x-modal name="dress-code-modal" max-width="lg" align="center" x-on:click="$openModal('blur-base')">
        <x-card title="Código de Vestimenta">
            <div class="space-y-4 text-gray-700 dark:text-gray-200">

                {{-- Aviso principal --}}
                <div
                    class="flex items-center gap-3 rounded-xl bg-red-50 dark:bg-red-950/30 border border-red-200 dark:border-red-800 px-4 py-3">
                    <x-icon name="exclamation-triangle" class="w-6 h-6 shrink-0 text-red-600 dark:text-red-400" />
                    <p class="font-bold uppercase tracking-wide text-red-700 dark:text-red-400 text-sm">
                        Debe presentarse de manera sobria
                    </p>
                </div>

                {{-- Lista de prohibiciones --}}
                <div class="rounded-xl border border-red-100 dark:border-red-900 bg-white dark:bg-secondary-800/50 p-4">
                    <h4
                        class="mb-3 flex items-center gap-2 font-bold text-red-700 dark:text-red-400 uppercase text-sm tracking-wide">
                        <x-icon name="x-circle" class="w-5 h-5 shrink-0" />
                        No está permitido:
                    </h4>
                    <ul class="space-y-2 text-sm text-gray-700 dark:text-gray-300">
                        <li class="flex items-start gap-2">
                            <x-icon name="x-mark" class="mt-0.5 w-4 h-4 shrink-0 text-red-500" />
                            Licras, mono deportivo, leggings, strapless.
                        </li>
                        <li class="flex items-start gap-2">
                            <x-icon name="x-mark" class="mt-0.5 w-4 h-4 shrink-0 text-red-500" />
                            Bermudas, shorts, pantalones rotos.
                        </li>
                        <li class="flex items-start gap-2">
                            <x-icon name="x-mark" class="mt-0.5 w-4 h-4 shrink-0 text-red-500" />
                            Blusas y franelas sin mangas.
                        </li>
                        <li class="flex items-start gap-2">
                            <x-icon name="x-mark" class="mt-0.5 w-4 h-4 shrink-0 text-red-500" />
                            Exponer el ombligo.
                        </li>
                        <li class="flex items-start gap-2">
                            <x-icon name="x-mark" class="mt-0.5 w-4 h-4 shrink-0 text-red-500" />
                            Vestidos o faldas cortas
                            <span class="text-gray-500 dark:text-gray-400">(debe estar por debajo de la rodilla).</span>
                        </li>
                    </ul>
                </div>

                {{-- Nota al pie --}}
                <p class="text-[10px] italic text-gray-500 dark:text-gray-400 text-center">
                    * Esta indicación aplica para el día de la cita presencial en el colegio.
                </p>
            </div>

            <x-slot name="footer">
                <div class="flex justify-end">
                    <x-button primary label="Entendido" x-on:click="close" />
                </div>
            </x-slot>
        </x-card>
    </x-modal>
</div>
