<div class="h-full w-full overflow-hidden rounded-[40px] bg-gradient-to-b from-green-400 via-green-600 to-black">
    <div class="flex h-full flex-col items-center justify-center px-4 text-center text-white">        
        <h2 class="mb-2 text-4xl font-extrabold uppercase mt-4">C.E. Colegio Fray Luis Amigó</h2>
        <div class="mb-2">
            <h1 class="text-2xl font-semibold">Censo Escolar 25-26 - Asistente</h1>
        </div>
        <p class="mb-12 text-lg">El primer paso hacia una educación de excelencia.</p>

        {{-- <div class="w-full max-w-sm space-y-4"> --}}
        <div class="w-full max-full space-y-4">
            <div class="rounded-lg {{ $currentStep == 1 ? 'bg-white/10' : 'bg-white/5' }} p-4 backdrop-blur-sm">
                <div class="flex items-center gap-4">
                    <span
                        class="flex h-8 w-8 items-center justify-center rounded-full {{ $currentStep == 1 ? 'bg-white text-black' : 'bg-white/20 text-white' }}">1</span>
                    <span class="text-lg">Validacion del email</span>
                </div>
            </div>
            <div class="rounded-lg {{ $currentStep == 2 ? 'bg-white/10' : 'bg-white/5' }} p-4 backdrop-blur-sm">
                <div class="flex items-center gap-4">
                    <span
                        class="flex h-8 w-8 items-center justify-center rounded-full {{ $currentStep == 2 ? 'bg-white text-black' : 'bg-white/20 text-white' }}">
                        2
                    </span>
                    <span class="text-lg">Datos del estudiante</span>
                </div>
            </div>
            <div class="rounded-lg {{ $currentStep == 3 ? 'bg-white/10' : 'bg-white/5' }} p-4 backdrop-blur-sm">
                <div class="flex items-center gap-4">
                    <span
                        class="flex h-8 w-8 items-center justify-center rounded-full {{ $currentStep == 3 ? 'bg-white text-black' : 'bg-white/20 text-white' }}">
                        3
                    </span>
                    <span class="text-lg">Datos del representante</span>
                </div>
            </div>

            <div class="rounded-lg {{ $currentStep == 4 ? 'bg-white/10' : 'bg-white/5' }} p-4 backdrop-blur-sm">
                <div class="flex items-center gap-4">
                    <span
                        class="flex h-8 w-8 items-center justify-center rounded-full {{ $currentStep == 4 ? 'bg-white text-black' : 'bg-white/20 text-white' }}">
                        4
                    </span>
                    <span class="text-lg">Guarda tu planilla de registro</span>
                </div>
            </div>

            <div class="rounded-lg {{ $currentStep == 4 ? 'bg-white/10' : 'bg-white/5' }} p-4 backdrop-blur-sm">
                <div class="flex items-center gap-4 cursor-pointer" wire:click="restart">
                    <span
                        class="flex h-8 w-8 items-center justify-center rounded-full {{ $currentStep == 4 ? 'bg-white text-black' : 'bg-white/20 text-white' }}">
                        <x-icon name="refresh" class="w-5 h-5" />
                    </span>
                    <span class="text-lg font-semibold">Empezar de nuevo</span>
                </div>
            </div>
        </div>
    </div>
</div>
