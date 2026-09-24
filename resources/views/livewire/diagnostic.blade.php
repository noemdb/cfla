<div class="min-h-screen text-white relative">
    @if ($currentView === 'student-identification')
        @include('livewire.diagnostic.student-identification')
    @elseif($currentView === 'dashboard')
        @include('livewire.diagnostic.dashboard')
    @elseif($currentView === 'wizard')
        @include('livewire.diagnostic.wizard')
    @elseif($currentView === 'summary')
        @include('livewire.diagnostic.summary')
    @elseif($currentView === 'guide')
        @include('livewire.diagnostic.guide')
    @endif

    {{-- Offline banner + retry --}}
    <div wire:offline class="fixed top-4 inset-x-0 z-[210] flex justify-center pointer-events-none">
        <div class="pointer-events-auto flex items-center gap-2 rounded-full bg-amber-600/90 backdrop-blur-md px-4 py-2 text-xs font-bold text-white shadow-xl border border-white/10">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636a9 9 0 010 12.728m0 0L12 12m6.364-6.364L12 12m0 0l-6.364 6.364m6.364-6.364L12 21"/></svg>
            Sin conexión — se reintentará automáticamente
        </div>
    </div>

    <style>.fixed.inset-0.bg-black\/50,.fixed.inset-0.bg-black\/60,.fixed.inset-0.bg-gray-900\/50,div[x-show].fixed.inset-0{backdrop-filter:blur(8px) !important;-webkit-backdrop-filter:blur(8px) !important}</style>
    {{-- Estado de carga global: solo píldora flotante (sin overlay borroso para verifyStudent) --}}
    <button type="button" disabled wire:loading.flex wire:target="verifyStudent,startDiagnostic,saveAnswer,nextQuestion,previousQuestion,finishDiagnostic,finalizeDiagnostic" style="display: none;"
        class="fixed bottom-6 right-6 z-[200] items-center gap-3 rounded-full bg-gray-900/90 backdrop-blur-xl pl-3 pr-5 py-3 text-sm font-semibold text-white shadow-2xl shadow-emerald-500/10 border border-emerald-500/20 ring-1 ring-white/5 cursor-wait">
        <span class="relative flex h-7 w-7 items-center justify-center rounded-full bg-emerald-500/15 border border-emerald-500/20">
            <svg class="h-4 w-4 animate-spin text-emerald-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <span class="absolute -top-0.5 -right-0.5 h-2 w-2 rounded-full bg-emerald-500 animate-ping"></span>
            <span class="absolute -top-0.5 -right-0.5 h-2 w-2 rounded-full bg-emerald-500"></span>
        </span>
        {{-- <span class="tracking-wide">Cargando</span> --}}
        {{-- <span class="h-3 w-px bg-white/10"></span> --}}
        <span class="text-xs font-normal text-white/60">Sincronizando…</span>
    </button>
</div>
