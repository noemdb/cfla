<div>
    <x-input wire:model="firstName" label="Name" placeholder="User's first name" />

    <x-select
        label="Select Status"
        placeholder="Select one status"
        :options="['Active', 'Pending', 'Stuck', 'Done']"
        wire:model.defer="model"
    />

</div>
