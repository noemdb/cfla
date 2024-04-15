<div>
    <x-input wire:model="firstName" label="Name" placeholder="User's first name" />

    <x-select
        label="Select Status"
        placeholder="Select one status"
        :options="['Active', 'Pending', 'Stuck', 'Done']"
        wire:model.defer="model"
    />
    <hr>
    alpine
    <div x-data="{ count: 0 }">
        <h2 x-text="count"></h2>    
        {{-- <button class="" x-on:click="count++">+</button> --}}
        <x-button positive label="Incrementar" x-on:click="count++"/>
    </div>

    <hr>

    Normal
    <div>
        <h1>{{ $count }}</h1>
     
        <button wire:click="increment">+</button>
     
        <button wire:click="decrement">-</button>
    </div>

</div>
