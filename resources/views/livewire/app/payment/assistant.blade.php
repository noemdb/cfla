<x-modal.card title="Reportes de pago" blur wire:model="modalAssistent" align="center">

    <div class="my-2 p-4 bg-emerald-900/30 border border-emerald-500/30 rounded-lg flex justify-between items-center">
        <div class="text-emerald-400 font-bold">
            <span class="text-xl font-bold">Representante encontrado.</span> <br> <span
                class="text-sm text-gray-200 text-left">Nombre: {{ $representant->name ?? null }}, CI:
                {{ $representant->ci_representant ?? null }}</span>
        </div>
        <div><x-badge.circle icon="check" positive class="w-10 h-10 bg-emerald-600 border-none text-white" /></div>
    </div>

    @php $width = ($limit>0) ? round((100 * $step / $limit)) : 0; @endphp
    <div class="mb-6 h-1.5 w-full bg-gray-800 rounded-full overflow-hidden">
        <div class="h-full bg-gradient-to-r from-emerald-600 to-green-400 rounded-full transition-all duration-500 ease-in-out shadow-[0_0_10px_rgba(16,185,129,0.5)]"
            style="width: {{ $width ?? null }}%"></div>
    </div>

    <div class="mb-6">
        <div class="{{ $step == 1 ? 'block' : 'hidden' }}" id="tabs-motive">
            @include('livewire.app.payment.stepper.motive')
            @include('livewire.app.payment.stepper.transactions')
        </div>
        {{-- <div class="{{($step==2) ? 'block' : 'hidden'}}" id="transactions">
            @include('livewire.app.payment.stepper.transactions')
        </div> --}}
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

</x-modal.card>
