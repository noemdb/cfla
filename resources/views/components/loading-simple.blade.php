@props([
    'message' => 'Cargando',
])

<div wire:loading
     class="fixed bottom-3 right-3 z-50 flex items-center gap-1.5 bg-gray-900/85 backdrop-blur-sm border border-white/10 rounded-md px-2.5 py-1.5 shadow shadow-black/20 transition-all duration-300"
     role="status">
    <div class="relative w-3 h-3">
        <div class="absolute inset-0 rounded-full border-1.5 border-white/10"></div>
        <div class="absolute inset-0 rounded-full border-1.5 border-transparent border-t-cyan-400 animate-spin"></div>
    </div>
    <span class="text-[10px] font-semibold text-gray-300 tracking-wider">{{ $message }}</span>
</div>
