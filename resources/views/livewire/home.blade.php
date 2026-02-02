<div>
    <x-input wire:model.blur="firstName" label="Name" placeholder="User's first name" />

    <x-select label="Select Status" placeholder="Select one status" wire:model="model">
        @foreach (['Active', 'Pending', 'Stuck', 'Done'] as $status)
            <x-select.option label="{{ $status }}" value="{{ $status }}" />
        @endforeach
    </x-select>
    <hr>

    <div x-data="{ count: 0 }">
        <h2 x-text="count"></h2>
        <x-button positive x-on:click="count++">Incrementar</x-button>
    </div>

    <hr>

    <div>
        <h1>Cont: {{ $count }}</h1>

        {{-- <button wire:click="increment">increment</button> --}}
        {{-- <button wire:click="decrement">decrement</button> --}}

        <x-button positive wire:click="increment">Incrementar</x-button>
        <x-button negative wire:click="decrement">Decrementar</x-button>

    </div>

</div>
