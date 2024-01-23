<div class="flex justify-between mt-4">
    <x-button secondary label="Anterior" wire:click="back({{$step}})"/>
    <x-button primary label="Siguiente" wire:click="next({{$step}})"/>
</div>
