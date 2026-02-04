<x-modal-card blur="md" title="Reportes de pago" wire:model="modalAssistent" align="center">

    <div
        class="my-4 p-4 bg-emerald-500/10 border border-emerald-500/20 rounded-xl flex justify-between items-center shadow-sm">
        <div class="space-y-1">
            <span class="text-lg font-bold text-emerald-500">Representante encontrado</span>
            <div class="text-sm text-gray-400">
                <span class="font-medium text-gray-300">{{ $representant->name ?? null }}</span>
                <span class="mx-1 text-gray-600">|</span>
                <span class="text-gray-400">CI: {{ $representant->ci_representant ?? null }}</span>
            </div>
        </div>
        <div class="bg-emerald-500/20 p-2 rounded-lg">
            <x-icon name="check-circle" class="w-8 h-8 text-emerald-500" />
        </div>
    </div>

    @php $width = ($limit>0) ? round((100 * $step / $limit)) : 0; @endphp
    <div class="mb-6 h-1.5 w-full bg-gray-800 rounded-full overflow-hidden">
        <div class="h-full bg-gradient-to-r from-emerald-600 to-green-400 rounded-full transition-all duration-500 ease-in-out shadow-[0_0_10px_rgba(16,185,129,0.5)]"
            style="width: {{ $width ?? null }}%"></div>
    </div>

    <div class="mb-6">
        <div class="{{ $step == 1 ? 'block' : 'hidden' }} p-2" id="tabs-motive">
            @include('livewire.app.payment.stepper.motive')
            @include('livewire.app.payment.stepper.transactions')
        </div>
        <div class="{{ $step == 2 ? 'block' : 'hidden' }}" id="confirm">
            @include('livewire.app.payment.stepper.confirm')
        </div>
    </div>

    @if ($step < $limit)
        <div class="flex justify-between mt-4">
            {{-- <x-button secondary label="Anterior" wire:click="back({{$step}})"/> --}}
            <x-button primary label="Siguiente" wire:click="validatedForStep({{ $step }})" />
        </div>
    @else
        <div class="flex justify-between mt-4">
            <x-button secondary label="Anterior" wire:click="back({{ $step }})" />
            <x-button positive label="Guardar" wire:click="save" />
        </div>
    @endif

</x-modal-card>
