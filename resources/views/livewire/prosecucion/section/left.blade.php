<div class="h-full w-full overflow-hidden rounded-[40px] bg-gradient-to-b from-green-400 via-green-600 to-black">
    <div class="flex h-full flex-col items-center justify-center px-4 text-center text-white">
        <h2 class="mb-2 text-4xl font-extrabold uppercase mt-4">U.E. Colegio Fray Luis Amigó</h2>
        <div class="mb-1">
            <h1 class="text-lg font-semibold">Prosecución Estudiantil 25-26
                <br>Asistente
            </h1>
        </div>
        <div class="text-lg">Confirma la continuidad educativa de tus representados.</div>

        <div class="w-full max-full space-y-4 mt-6">
            {{-- Paso 1 --}}
            <div class="rounded-lg {{ $step == 1 ? 'bg-white/10' : 'bg-white/5' }} p-4 backdrop-blur-sm">
                <div class="flex items-center gap-3">
                    <span
                        class="flex h-8 w-8 items-center justify-center rounded-full {{ $step == 1 ? 'bg-white text-black' : 'bg-white/20 text-white' }}">1</span>
                    <span class="text-lg">Identificación</span>
                </div>
            </div>

            {{-- Paso 2 --}}
            <div class="rounded-lg {{ $step == 2 ? 'bg-white/10' : 'bg-white/5' }} p-4 backdrop-blur-sm">
                <div class="flex items-center gap-3">
                    <span
                        class="flex h-8 w-8 items-center justify-center rounded-full {{ $step == 2 ? 'bg-white text-black' : 'bg-white/20 text-white' }}">
                        2
                    </span>
                    <span class="text-lg">Estudiantes</span>
                </div>
            </div>

            {{-- Paso 3 --}}
            <div class="rounded-lg {{ $step == 3 ? 'bg-white/10' : 'bg-white/5' }} p-4 backdrop-blur-sm">
                <div class="flex items-center gap-3">
                    <span
                        class="flex h-8 w-8 items-center justify-center rounded-full {{ $step == 3 ? 'bg-white text-black' : 'bg-white/20 text-white' }}">
                        3
                    </span>
                    <span class="text-lg">Confirmación</span>
                </div>
            </div>

            {{-- Botón Reset --}}
            <div class="rounded-lg bg-white/5 p-4 backdrop-blur-sm hover:bg-white/10 transition">
                <div class="flex items-center gap-3 cursor-pointer" wire:click="resetWizard">
                    <span class="flex h-8 w-8 items-center justify-center rounded-full bg-white/20 text-white">
                        <x-icon name="arrow-path" class="w-5 h-5" />
                    </span>
                    <span class="text-lg font-semibold">Empezar de nuevo</span>
                </div>
            </div>
        </div>
    </div>

</div>
