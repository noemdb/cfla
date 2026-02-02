<div class="space-y-6 text-white text-center">
    <div class="flex flex-col items-center">
        <div
            class="w-32 h-32 border-4 border-emerald-500 rounded-full bg-emerald-900/30 mb-6 flex justify-center items-center backdrop-blur-sm">
            <img class="w-20 h-20 object-contain" src="{{ asset('image/highlighted/matricula.png') }}" alt="Matrícula" />
        </div>
        <h2 class="text-3xl font-bold text-emerald-400">Actualización de Datos</h2>
        <p class="text-gray-400 mt-2">Proceso de solicitud de matrícula 2025-2026</p>
    </div>

    <div class="bg-white/5 border border-white/10 p-6 rounded-2xl backdrop-blur-md text-left">
        <p class="text-sm leading-relaxed text-gray-300">
            <span class="font-bold text-emerald-400 block mb-2">Instrucciones:</span>
            Para iniciar el proceso de actualización de matrícula, asegúrese de tener a mano la cédula de identidad del
            representante legal. Este sistema le permitirá actualizar la información personal, de contacto y dirección
            del estudiante.
        </p>
    </div>

    <div class="pt-4">
        <x-button label="Comenzar Proceso" wire:click="setStart"
            class="w-full py-4 rounded-xl font-bold uppercase tracking-wider bg-emerald-600 hover:bg-emerald-500 text-white border-none shadow-[0_0_20px_rgba(16,185,129,0.3)] transition-all duration-300" />
    </div>
</div>
