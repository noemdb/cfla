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

    <x-card title="Your title here">
        Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book
    </x-card>

    <livewire:counter />

    <div x-data="{ count: 0 }">
        <button @click="count++">Add</button>
        <span x-text="count">0</span>
    </div>

    @wireUiScripts
    
    @livewireScripts
</body>

</html>
