<div>
    <div style="text-align: center">
        <div class="flex justify-center mt-16 sm:items-center sm:justify-between gap-4">
            <button class=" border rounded p-4" wire:click="increment">+</button>
            <h1>{{ $count }}</h1>
            <button class=" border rounded p-4" wire:click="decrement">-</button>
        </div>
    </div>    
</div>
