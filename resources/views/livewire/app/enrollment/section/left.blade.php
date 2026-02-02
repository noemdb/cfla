<div class="h-full w-full overflow-hidden rounded-[40px] bg-gradient-to-b from-emerald-400 via-emerald-600 to-black">
    <div class="flex h-full flex-col items-center justify-center px-4 text-center text-white">
        <h2 class="mb-2 text-4xl font-extrabold uppercase mt-4">U.E. Colegio Fray Luis Amigó</h2>
        <div class="mb-1">
            <h1 class="text-2xl font-semibold">Solicitud de Matrícula - Asistente</h1>
        </div>
        <div class="text-lg">Actualización de datos para el año escolar.</div>
        <div class="mb-8 font-semibold">Proceso administrativo de inscripción.</div>

        <div class="w-full max-full space-y-4">
            <div class="rounded-lg {{ $currentStep == 1 ? 'bg-white/10' : 'bg-white/5' }} p-4 backdrop-blur-sm">
                <div class="flex items-center gap-4">
                    <span
                        class="flex h-8 w-8 items-center justify-center rounded-full {{ $currentStep == 1 ? 'bg-white text-black' : 'bg-white/20 text-white' }}">1</span>
                    <span class="text-lg">Instrucciones</span>
                </div>
            </div>
            <div class="rounded-lg {{ $currentStep == 2 ? 'bg-white/10' : 'bg-white/5' }} p-4 backdrop-blur-sm">
                <div class="flex items-center gap-4">
                    <span
                        class="flex h-8 w-8 items-center justify-center rounded-full {{ $currentStep == 2 ? 'bg-white text-black' : 'bg-white/20 text-white' }}">
                        2
                    </span>
                    <span class="text-lg">Validación de Representante</span>
                </div>
            </div>

            <div class="rounded-lg {{ $currentStep == 2 ? 'bg-white/10' : 'bg-white/5' }} p-4 backdrop-blur-sm">
                <div class="flex items-center gap-4 cursor-pointer" wire:click="restart">
                    <span class="flex h-8 w-8 items-center justify-center rounded-full bg-white/20 text-white">
                        <x-icon name="arrow-path" class="w-5 h-5" />
                    </span>
                    <span class="text-lg font-semibold">Empezar de nuevo</span>
                </div>
            </div>
        </div>
    </div>
</div>
