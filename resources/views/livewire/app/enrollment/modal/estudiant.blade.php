<x-modal-card title="Estudiante habilitado" blur wire:model="modalAssistent" max-width="sm">

    <div class=" my-2 ml-2 bg-green-100 p-2 rounded-sm flex justify-between items-center">
        <div class="text-green-600 font-bold">
            <span class="text-xl font-bold">Estudiante habilitado para la solitud de matrícula.</span> <br> <span class="text-xs text-gray-400 text-left">Nombre: {{$census->fullname ?? null}}</span>         
        </div>
        <div><x-mini-badge icon="check" positive class="w-8 h-8"/></div>  
    </div> 

    @php $width = ($limit>0) ? round((100 * $step / $limit)) : 0; @endphp
    <div class="mb-6 h-1 w-full bg-neutral-200 dark:bg-neutral-600">
        <div class="h-1 bg-green-500" style="width: {{$width ?? null}}%"></div>
    </div>
    
    <div class=" border rounded shadow-sm py-2">
        @include('livewire.app.enrollment.steper.index')
    </div>

    @if ($step<$limit)
        <div class="flex justify-between mt-4">
            <x-button secondary label="Anterior" wire:click="back({{$step}})"/>
            <x-button primary label="Siguiente" wire:click="getValidate({{$step}})" />
        </div>
    @else
        <div class="flex justify-between mt-4">
            <x-button secondary label="Anterior" wire:click="back({{$step}})"/>
            <x-button positive label="Guardar" wire:click="save"/>
        </div>
    @endif    

</x-modal-card>