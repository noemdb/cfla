<div>
    <x-input wire:model="firstName" label="Name" placeholder="User's first name" />

    <x-select
        label="Select Status"
        placeholder="Select one status"
        :options="['Active', 'Pending', 'Stuck', 'Done']"
        wire:model.defer="model"
    />
    <hr>
    
    <div x-data="{ count: 0 }">
        <h2 x-text="count"></h2>    
        <x-button positive label="Incrementar" x-on:click="count++"/>
    </div>

    <hr>

    <div>
        <h1>Cont: {{ $count }}</h1>

        {{-- <button wire:click="increment">increment</button> --}}
        {{-- <button wire:click="decrement">decrement</button> --}}

        <x-button positive label="Incrementar" wire:click="increment"/>
        <x-button negative label="Decrementar" wire:click="decrement"/>     
        
    </div>

</div>
