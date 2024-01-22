<div>
    @include('livewire.app.enrollment.start')
    @include('livewire.app.enrollment.assistant')

    {{-- <form wire:submit.prevent="save">

        <input type="text" wire:model="post.title">

        <textarea wire:model="post.content"></textarea>

        <button type="submit">Save</button>
        
    </form>

    <x-modal wire:model.defer="simpleModal">
        <x-card title="Consent Terms">
            <p class="text-gray-600">
                Lorem Ipsum...
            </p>
            <x-slot name="footer">
                <div class="flex justify-end gap-x-4">
                    <x-button flat label="Cancel" x-on:click="close" />
                    <x-button primary label="I Agree" />
                </div>
            </x-slot>
        </x-card>
    </x-modal> --}}
</div>
