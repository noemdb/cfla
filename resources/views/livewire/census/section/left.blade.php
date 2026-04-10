<div class="h-full w-full overflow-hidden rounded-[40px] bg-gradient-to-b from-green-400 via-green-600 to-black">
    <div class="flex h-full flex-col items-center justify-center px-4 text-center text-white">
        <h2 class="mb-2 text-4xl font-extrabold uppercase mt-4">U.E. Colegio Fray Luis Amigó</h2>
        <div class="mb-1">
            <h1 class="text-2xl font-semibold">Censo Escolar 26-27 - Asistente</h1>
        </div>
        <div class="text-lg">El primer paso hacia una educación de excelencia.</div>
        <div class="mb-8 font-semibold">Primera convocatoria: 27 de abril al 30 de mayo, de 8am a 2pm.</div>

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
            <div
                class="rounded-lg bg-white/5 p-4 backdrop-blur-sm hover:bg-white/10 transition">
                <div class="flex items-center gap-4 cursor-pointer" onclick="$openModal('dress-code-modal')">
                    <span
                        class="flex h-8 w-8 items-center justify-center rounded-full bg-white/20 text-white">
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
    <x-modal name="dress-code-modal" max-width="2xl" align="center">
        <x-card title="Resumen del Código de Vestimenta">
            <div class="space-y-6 text-gray-700 dark:text-gray-200">
                <section>
                    <h3 class="flex items-center gap-2 font-bold text-teal-700 dark:text-teal-400 uppercase border-b border-teal-100 dark:border-teal-900 pb-1 mb-2">
                        <x-icon name="academic-cap" class="w-5 h-5" />
                        Educación Primaria (1° a 6° Grado)
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                        <div class="bg-teal-50 dark:bg-teal-950/30 p-3 rounded-lg border border-teal-100 dark:border-teal-900">
                            <span class="font-bold block text-teal-800 dark:text-teal-300 mb-1">Uniforme Diario:</span>
                            Pantalón azul marino, chemise blanca con insignia, zapatos negros/marrón, medias blancas, abrigo azul marino.
                        </div>
                        <div class="bg-teal-50 dark:bg-teal-950/30 p-3 rounded-lg border border-teal-100 dark:border-teal-900">
                            <span class="font-bold block text-teal-800 dark:text-teal-300 mb-1">Educación Física:</span>
                            Mono azul marino, franela blanca con insignia, zapatos deportivos negros o marrón.
                        </div>
                    </div>
                </section>

                <section>
                    <h3 class="flex items-center gap-2 font-bold text-teal-700 dark:text-teal-400 uppercase border-b border-teal-100 dark:border-teal-900 pb-1 mb-2">
                        <x-icon name="book-open" class="w-5 h-5" />
                        Educación Media General
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                        <div class="bg-teal-50 dark:bg-teal-950/30 p-3 rounded-lg border border-teal-100 dark:border-teal-900">
                            <span class="font-bold block text-teal-800 dark:text-teal-300 mb-1">1° a 3° Año:</span>
                            Chemise <strong class="text-teal-900 dark:text-teal-200">azul celeste</strong> con insignia. Resto igual a primaria.
                        </div>
                        <div class="bg-teal-50 dark:bg-teal-950/30 p-3 rounded-lg border border-teal-100 dark:border-teal-900">
                            <span class="font-bold block text-teal-800 dark:text-teal-300 mb-1">4° y 5° Año:</span>
                            Chemise <strong class="text-teal-900 dark:text-teal-200">beige</strong> con insignia, zapatos exclusivamente negros.
                        </div>
                    </div>
                </section>

                <section class="bg-gray-50 dark:bg-secondary-800/50 p-4 rounded-xl border border-gray-200 dark:border-secondary-700">
                    <h4 class="font-bold text-gray-800 dark:text-gray-100 mb-2 flex items-center gap-2">
                        <x-icon name="check-circle" class="w-5 h-5 text-green-600 dark:text-green-400" />
                        Normas Generales de Presentación:
                    </h4>
                    <ul class="list-disc list-inside space-y-1 text-sm text-gray-600 dark:text-gray-400">
                        <li>Uniforme completo, limpio y en buen estado.</li>
                        <li>Cabello en tono natural (sin tintes o accesorios extravagantes).</li>
                        <li><strong>Varones:</strong> Cabello corto convencional, sin barba o bigote.</li>
                        <li><strong>Hembras:</strong> Uñas cortas, solo brillo o color discreto; sin maquillaje.</li>
                    </ul>
                </section>

                <p class="text-[10px] italic text-gray-500 dark:text-gray-400 text-center">
                    * El incumplimiento genera correctivos pedagógicos según los Acuerdos de Convivencia.
                </p>
            </div>

            <x-slot name="footer">
                <div class="flex justify-end gap-x-4">
                    <x-button flat label="Cerrar" x-on:click="close" />
                    <x-button primary label="Entendido" x-on:click="close" />
                </div>
            </x-slot>
        </x-card>
    </x-modal>
</div>
