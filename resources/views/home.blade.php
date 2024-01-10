<!doctype html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @vite('resources/css/app.css')
    @vite(['resources/js/app.js'])

    @livewireStyles
</head>

<body>
    <h1 class="text-3xl font-bold underline">
        Hello world!
    </h1>

    <x-input wire:model="firstName" label="Name" placeholder="User's first name" />

    <livewire:counter />

    <div x-data="{ count: 0 }">
        <button @click="count++">Add</button>
        <span x-text="count">0</span>
    </div>


    <wireui:scripts />
    
    @livewireScripts
</body>

</html>
