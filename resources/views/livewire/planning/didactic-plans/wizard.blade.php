<div>
    @if($open)
        <x-modal-card title="Plan de clases / secuencia didáctica" blur="lg" wire:model="open" width="max-w-[90vw]"
            class="border border-gray-200 dark:border-white/10 rounded-xl bg-white dark:bg-gray-900">
            {{-- Stepper --}}
            <div class="flex items-center gap-1.5 mb-4">
                @foreach([1 => 'Filtros y periodo', 2 => 'Actividades', 3 => 'Consolidación', 4 => 'Aprobación', 5 => 'PDF'] as $n => $label)
                    <div class="flex-1 flex items-center justify-center gap-1.5 px-2 py-2 rounded-lg border text-[10px] font-bold uppercase tracking-widest
                        {{ $step == $n ? 'bg-emerald-500/15 text-emerald-600 dark:text-emerald-400 border-emerald-500/30' : ($step > $n ? 'bg-emerald-500/5 text-emerald-600 dark:text-emerald-500 border-emerald-500/20' : 'bg-gray-50 dark:bg-white/5 text-gray-400 border-gray-200 dark:border-white/5') }}">
                        <span class="inline-flex items-center justify-center w-5 h-5 rounded-full text-[10px] {{ $step >= $n ? 'bg-emerald-500 text-white' : 'bg-gray-200 dark:bg-white/10 text-gray-500' }}">{{ $n }}</span>
                        <span class="hidden sm:inline">{{ $label }}</span>
                    </div>
                    @if($n < 5)
                        <div class="w-2 h-px {{ $step > $n ? 'bg-emerald-500' : 'bg-gray-200 dark:bg-white/10' }}"></div>
                    @endif
                @endforeach
            </div>

            <div class="w-[86vw] max-w-[86vw] max-h-[68vh] overflow-y-auto pr-1">
                @if($stepError)
                    <div class="mb-4 p-3 rounded-lg border border-red-200 dark:border-red-500/20 bg-red-50 dark:bg-red-500/10 text-xs text-red-700 dark:text-red-300 font-medium">
                        {{ $stepError }}
                    </div>
                @endif

                @if($step === 1)
                    @include('livewire.planning.didactic-plans.partials.step-1')
                @elseif($step === 2)
                    @include('livewire.planning.didactic-plans.partials.step-2')
                @elseif($step === 3)
                    @include('livewire.planning.didactic-plans.partials.step-3')
                @elseif($step === 4)
                    @include('livewire.planning.didactic-plans.partials.step-4')
                @else
                    @include('livewire.planning.didactic-plans.partials.step-5')
                @endif
            </div>

            <x-slot name="footer">
                <div class="flex flex-wrap items-center justify-between gap-2 w-full">
                    <div class="flex items-center gap-2">
                        <x-button flat label="Cerrar" wire:click="close" />
                        @if($step > 1 && $step < 5)
                            <x-button flat label="Atrás" wire:click="back" />
                        @endif
                    </div>

                    <div class="flex flex-wrap items-center justify-end gap-2">
                        @if($step === 1)
                            <x-button primary label="Continuar" wire:click="goToActivities" spinner="goToActivities" />
                        @elseif($step === 2)
                            <x-button flat label="Plan manual" wire:click="manualPlan" />
                            <x-button primary label="Consolidar selección" wire:click="buildSessions" spinner="buildSessions" />
                        @elseif($step === 3)
                            <x-button primary label="Revisar" wire:click="goToReview" spinner="goToReview" />
                        @elseif($step === 4)
                            <x-button flat label="Guardar borrador" wire:click="saveDraft" spinner="saveDraft" />
                            <x-button primary label="Aprobar y guardar" wire:click="approve" spinner="approve" />
                        @else
                            @if($hasDraftResults)
                                <x-button primary label="Aprobar borradores" wire:click="approveDrafts" spinner="approveDrafts" />
                            @endif
                        @endif
                    </div>
                </div>
            </x-slot>
        </x-modal-card>
    @endif
</div>
