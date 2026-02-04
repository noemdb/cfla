<hr>

<livewire:counter />

<hr>

<x-input wire:model="firstName" label="Name" placeholder="User's first name" />

<hr>

<x-select label="Select Status" placeholder="Select one status" :options="['Active', 'Pending', 'Stuck', 'Done']" wire:model.live="model" />

<hr>

<div class="flex justify-end">
    <x-dropdown>
        <x-dropdown.item label="Settings" />
        <x-dropdown.item label="My Profile" />
        <x-dropdown.item label="Logout" />
    </x-dropdown>
</div>



<hr>

<x-color-picker label="Select a Color" placeholder="Select the car color" />

<x-notifications />

<x-dialog />

<x-modal wire:model.live="simpleModal" blur>
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
</x-modal>
