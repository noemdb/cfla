@props([
    'message' => 'Cargando',
])

<div wire:loading
     class="fixed bottom-4 right-4 z-50 flex items-center gap-2.5 bg-gray-900/85 backdrop-blur-md border border-white/10 rounded-lg px-3.5 py-2.5 shadow-lg shadow-black/20 transition-all duration-300"
     role="status">
    <div class="relative w-4 h-4">
        <div class="absolute inset-0 rounded-full border-2 border-white/10"></div>
        <div class="absolute inset-0 rounded-full border-2 border-transparent border-t-cyan-400 animate-spin"></div>
    </div>
    <span class="text-[11px] font-semibold text-gray-300 tracking-wider">{{ $message }}</span>
</div>
