<div class="flex justify-between items-center px-4 py-3 bg-gray-900 border-b border-emerald-500/20">
    <div class="flex items-center space-x-3">
        <div class="bg-gray-800 p-1.5 rounded-lg border border-emerald-500/30 shadow-sm">
            <img src="{{ asset('image/brand/512.png') }}" class="w-8 h-8 object-contain" alt="Logo">
        </div>
        <div>
            <h1 class="text-base font-bold text-gray-100 leading-tight tracking-tight">SAEFL</h1>
            <p class="text-[10px] text-emerald-400 font-bold uppercase tracking-wider">Sistema de Pagos</p>
        </div>
    </div>

    <div>
        <a href="{{ route('home') }}" class="p-2 text-gray-400 hover:text-emerald-400 transition-colors">
            <x-icon name="home" class="w-5 h-5" />
        </a>
    </div>
</div>
