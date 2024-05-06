<x-modal.card title="Reportes de pago" blur wire:model="modalAssistent" max-width="2xl" align="start">

    <div class=" my-2 ml-2 bg-green-100 p-2 rounded-sm flex justify-between items-center">
        <div class="text-green-600 font-bold">
            <span class="text-xl font-bold">Representante encontrado.</span> <br> <span class="text-xs text-gray-400 text-left">Nombre: {{$representant->name ?? null}}, CI: {{$representant->ci_representant ?? null}}</span>         
        </div>
        <div><x-badge.circle icon="check" positive class="w-8 h-8"/></div>  
    </div> 

    @php $width = ($limit>0) ? round((100 * $step / $limit)) : 0; @endphp
    <div class="mb-6 h-1 w-full bg-neutral-200 dark:bg-neutral-600">
        <div class="h-1 bg-green-500" style="width: {{$width ?? null}}%"></div>
    </div>

    <div class="mb-6">
        <div class="{{($step==1) ? 'block' : 'hidden'}}" id="tabs-motive">
            @include('livewire.app.payment.stepper.motive')
            @include('livewire.app.payment.stepper.transactions')
        </div>
        {{-- <div class="{{($step==2) ? 'block' : 'hidden'}}" id="transactions">
            @include('livewire.app.payment.stepper.transactions')
        </div> --}}
        <div class="{{($step==2) ? 'block' : 'hidden'}}" id="confirm">
            @include('livewire.app.payment.stepper.confirm')
        </div>
    </div>

    @if ($step<$limit)
        <div class="flex justify-between mt-4">
            {{-- <x-button secondary label="Anterior" wire:click="back({{$step}})"/> --}}
            <x-button primary label="Siguiente" wire:click="validatedForStep({{$step}})" />
        </div>
    @else
        <div class="flex justify-between mt-4">
            <x-button secondary label="Anterior" wire:click="back({{$step}})"/>
            <x-button positive label="Guardar" wire:click="save"/>
        </div>
    @endif 

</x-modal.card>