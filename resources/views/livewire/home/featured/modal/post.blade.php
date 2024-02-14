<x-modal.card title="{{$post->title ?? null}}" blur wire:model="modalShow" max-width="lg">

    <div class="bg-blue-100 p-1 rounded-sm flex justify-between items-center">
        <div class="text-blue-600">
            <span class="text-lg font-light">
                {{$post->description ?? null}}.
            </span>
            <br>
            <div class="text-xs text-gray-400 text-left">
                Creado: {{$post->created_at ?? null}} || Actualizado: {{$post->updated_at ?? null}}
            </div>         
        </div> 
    </div>

    <div class="text-xs border-t-2 mt-2 max-w-full overflow-hidden text-wrap word-break">
        {{ $post->body,100 ?? null }}
    </div>
    
    <div class="text-xs border-t-2 mt-2 max-w-full overflow-hidden text-wrap word-break">
        {!! $post->insert ?? null !!}
    </div>

    {{-- @php $width = ($limit>0) ? round((100 * $step / $limit)) : 0; @endphp
    <div class="mb-6 h-1 w-full bg-neutral-200 dark:bg-neutral-600">
        <div class="h-1 bg-green-500" style="width: {{$width ?? null}}%"></div>
    </div>
    
    <div class="border rounded shadow-sm py-2">
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
    @endif     --}}

</x-modal.card>