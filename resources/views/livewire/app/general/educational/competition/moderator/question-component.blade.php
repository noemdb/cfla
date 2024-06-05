<div>
    <x-card>

        <x-slot name="title">
            <div class="flex px-2 items-center justify-center mb-2">            
                <h5 class="grow ms-2 text-2xl font-bold tracking-tight text-gray-900 dark:text-white">
                    {{$debate->name}}
                </h5>

                @if ($debate->status_active)
                    <x-badge.circle negative icon="pause" class="ms-2 px-2 animate-pulse"/>
                @endif
            </div>
        </x-slot>

        <x-slot name="action">
            <x-dropdown>
                @if ($debate->status_active)
                    <x-dropdown.item label="Desactivar Debate" wire:click="setOffline({{$debate->id}})"/>
                @else
                    <x-dropdown.item label="Activar Debate" wire:click="setOnline({{$debate->id}})"/>
                @endif                
                {{-- <x-dropdown.item label="Planilla de Resultados" /> --}}
            </x-dropdown>
        </x-slot> 

        <div class="py-2">
            {{-- <x-select label="Seleccione una Categoría" placeholder="Seleccione una Categoría" wire:model.live="category" :options="$list_category"/> --}}
            <x-select placeholder="Seleccione una Categoría" wire:model.live="category" :options="$list_category"/>
        </div>

        @if ($questions->isNotEmpty())
            @include('livewire.app.general.educational.competition.moderator.partials.questions')            
        @else
            @if ($category)
                <div>No hay preguntas registradas</div>
            @else
                <div>Seleccione una Categoría</div>
            @endif            
        @endif

    </x-card>

</div>