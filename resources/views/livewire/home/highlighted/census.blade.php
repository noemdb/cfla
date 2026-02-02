<div class="h-full flex flex-col">
    <!-- Header -->
    <div class="flex items-center space-x-3 mb-4">
        <div class="p-2 bg-emerald-900/50 rounded-lg border border-emerald-500/30">
            <x-icon name="bars-3" class="w-8 h-8 text-emerald-400" />
        </div>
        <h3 class="text-lg md:text-xl font-bold text-emerald-100 uppercase tracking-wide">
            Censo Escolar 25-26 - Asistente
        </h3>
    </div>

    <!-- Content -->
    <div class="flex-1 flex flex-col">
        <div class="relative overflow-hidden rounded-xl mb-4 group">
            <div class="flex justify-center bg-gray-800/50 p-6 rounded-xl border border-emerald-500/20">
                <div
                    class="grid place-items-center h-24 w-24 bg-emerald-900/30 rounded-full border border-emerald-500/30 shadow-[0_0_15px_rgba(16,185,129,0.2)]">
                    <x-icon name="document-chart-bar" class="w-12 h-12 text-emerald-400" />
                </div>
            </div>

            <div class="text-center mt-4">
                <div class="text-xl font-semibold text-gray-200 mb-2">El primer paso hacia una educación de excelencia.
                </div>
                <div class="text-sm text-emerald-300 font-medium mb-2">11va Jornada, desde el 01 hasta el 22 de
                    septiembre de 8am a 2pm.</div>
            </div>
        </div>

        <p class="text-sm text-gray-400 text-center mb-6 leading-relaxed flex-1">
            Nos complace poder ofrecerles a sus hijos la oportunidad de formar parte de nuestra comunidad educativa, que
            está comprometida con la excelencia académica y el desarrollo integral de los estudiantes.
        </p>

        <div class="mt-auto">
            <x-button positive
                class="w-full bg-emerald-600 hover:bg-emerald-500 border-none shadow-lg shadow-emerald-500/20"
                :href="route('census')">
                Comenzar
            </x-button>
        </div>
    </div>
</div>
