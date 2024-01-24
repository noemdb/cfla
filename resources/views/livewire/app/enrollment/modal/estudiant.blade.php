<x-modal.card title="Estudiante habilitado" blur wire:model.defer="modalAssistent">

    <div class=" my-2 ml-2 bg-green-100 p-2 rounded-sm flex justify-between">
        <div class="text-green-600 font-bold">
            <span class="text-xl font-bold">Estudiante habilitado para la solitud de matrícula.</span> <br> <span class="text-xm text-gray-400 text-left">Nombre: {{$census->fullname ?? null}}</span>         
        </div>
        <div><x-badge.circle icon="check" positive class="w-8 h-8"/></div>  
    </div> 
    
    <div class="flex justify-center items-center my-2">
        <x-badge.circle lg primary label="{{$step}}" class="w-15 h-15 p-2" />  <span class="text-sm text-gray-400 p-2">de {{$limit}}</span>
    </div>

    <div class=" border rounded shadow-sm py-2">
        @include('livewire.app.enrollment.steper.index')
    </div>

    @if ($step<$limit)
        <div class="flex justify-between mt-4">
            <x-button secondary label="Anterior" wire:click="back({{$step}})"/>
            <x-button primary label="Siguiente" wire:click="getValidate({{$step}})"/>
        </div>
    @else
        <div class="flex justify-between mt-4">
            <x-button secondary label="Anterior" wire:click="back({{$step}})"/>
            <x-button positive label="Guardar" wire:click="save"/>
        </div>
    @endif

    

</x-modal.card>