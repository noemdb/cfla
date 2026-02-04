<x-modal-card blur="md" wire:model="modalSearch" align="center" max-width="md">
    <x-card class="!bg-gray-900 !border-white/10 shadow-2xl shadow-emerald-500/10 overflow-hidden relative">
        <div class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-emerald-500 to-green-600"></div>

        <div class="space-y-6 pt-4">
            <!-- Header Info -->
            <div class="text-center mb-2">
                <div
                    class="inline-flex justify-center items-center p-4 bg-emerald-500/10 rounded-2xl border border-emerald-500/20 mb-4 shadow-lg shadow-emerald-500/5">
                    <x-icon name="clipboard-document-check" class="w-12 h-12 text-emerald-500" />
                </div>
                <h3 class="text-xl font-bold text-white tracking-tight">Asistente de Registro</h3>
                <p class="text-xs text-emerald-500/80 font-semibold uppercase tracking-widest mt-1">Período 2025 - 2026
                </p>
            </div>

            <!-- Input Form -->
            <div class="bg-black/40 p-6 rounded-lg border border-white/5 space-y-6 shadow-2xl">
                <x-input label="Cédula del Representante" placeholder="Ej: 12345678" wire:model="ci"
                    icon="identification" hint="Sin puntos ni guiones"
                    class="!bg-gray-950 !border-gray-800 focus:!border-emerald-500/50 focus:!ring-4 focus:!ring-emerald-500/10 !text-gray-100 placeholder:!text-gray-600 transition-all shadow-inner" />

                <x-button primary xl full label="Continuar" icon="check" wire:click="search"
                    class="!bg-emerald-600 hover:!bg-emerald-500 !border-none shadow-xl shadow-emerald-500/10 hover:shadow-emerald-500/20 transition-all font-black uppercase tracking-widest py-4" />
            </div>

            <!-- Helpful Note -->
            <div class="flex items-start gap-3 p-4 bg-emerald-950 rounded-xl border border-emerald-500/10">
                <x-icon name="information-circle" class="w-5 h-5 text-emerald-500 shrink-0 mt-0.5" />
                <p class="text-[11px] leading-relaxed text-gray-400">
                    La cédula se utiliza para verificar sus datos y asociar correctamente el reporte de pago en nuestra
                    base
                    de datos institucional <span class="text-emerald-500 font-bold">SAEFL</span>.
                </p>
            </div>
        </div>
    </x-card>
</x-modal-card>
