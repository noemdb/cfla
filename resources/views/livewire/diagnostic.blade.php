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

    {{-- Estado de carga global: botón flotante abajo a la derecha --}}
    <button type="button" disabled wire:loading.flex
        class="fixed bottom-6 right-6 z-[200] items-center gap-2 rounded-full bg-emerald-600/80 backdrop-blur-md px-5 py-3 text-sm font-medium text-white shadow-xl shadow-emerald-950/40 border border-white/10 cursor-wait">
        <svg class="h-4 w-4 animate-spin text-white" xmlns="http://www.w3.org/2000/svg" fill="none"
            viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor"
                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
            </path>
        </svg>
        <span>Cargando ...</span>
    </button>
</div>
