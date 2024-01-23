<!-- resources/views/layouts/app.blade.php -->

<html class="scroll-smooth">

<head>
    <title>{{env('APP_NAME')}} - @yield('title')</title>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    @vite('resources/css/app.css')
    @vite(['resources/js/app.js'])

    <wireui:scripts />
    @livewireStyles

</head>

<body class="bg-white dark:bg-black">

    <livewire:home />

    

    {{-- <x-modal.card title="Edit Customer" wire:model.defer="cardModal">
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <x-input label="Name" placeholder="Your full name" />
            <x-input label="Phone" placeholder="USA phone" />

            <div class="col-span-1 sm:col-span-2">
                <x-input label="Email" placeholder="example@mail.com" />
            </div>

            <div
                class="col-span-1 sm:col-span-2 cursor-pointer bg-gray-100 rounded-xl shadow-md h-72 flex items-center justify-center">
                <div class="flex flex-col items-center justify-center">
                    <x-icon name="cloud-upload" class="w-16 h-16 text-blue-600" />
                    <p class="text-blue-600">Click or drop files here</p>
                </div>
            </div>
        </div>

        <x-slot name="footer">
            <div class="flex justify-between gap-x-4">
                <x-button flat negative label="Delete" wire:click="delete" />

                <div class="flex">
                    <x-button flat label="Cancel" x-on:click="close" />
                    <x-button primary label="Save" wire:click="save" />
                </div>
            </div>
        </x-slot>
    </x-modal.card> --}}

    <div id="header"> @yield('header') </div>

    {{-- <div id="aside"> @yield('aside') </div> --}}

    <div id="hero" class="p-1"> @yield('hero') </div>

    <div id="highlighted" class="p-1"> @yield('highlighted') </div>

    <div id="featured" class="p-1"> @yield('featured') </div>

    <div id="category" class="p-1"> @yield('category') </div>

    <div id="gallery" class="p-1"> @yield('gallery') </div>

    <div id="services" class="p-1"> @yield('services') </div>

    <div id="alliances" class="p-1"> @yield('alliances') </div>

    <div id="autority" class="p-1"> @yield('autority') </div>

    <div id="workers" class="p-1"> @yield('workers') </div>

    <div id="testimonials" class="p-1"> @yield('testimonials') </div>

    <div id="timeline" class="p-1"> @yield('timeline') </div>

    <div id="graphs" class="p-1"> @yield('graphs') </div>

    <div id="contacts" class="p-1"> @yield('contacts') </div>

    <div id="socials" class="p-1"> @yield('socials') </div>

    <div id="container" class="p-1"> @yield('content') </div>

    <div id="footer"> @yield('footer') </div>

    @yield('scripts')

    @livewireScripts

</body>

</html>