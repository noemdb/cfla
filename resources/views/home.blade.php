@extends('layouts.home')

@section('title', 'U.E. Colegio Fray Luis Amigó')

@section('content')
    <div class="space-y-12">
        {{-- Hero Section --}}
        <section id="hero" class="relative">
            <livewire:home.hero-component />
        </section>

        {{-- Highlighted Section --}}
        <section id="highlighted" class="container mx-auto px-1">
            <livewire:home.highlighted-component />
        </section>

        {{-- Pensums Section --}}
        <section id="pensums" class="container mx-auto px-1">
            @include('home.pensums.main')
        </section>

        {{-- Featured Section --}}
        <section id="featured" class="container mx-auto px-1">
            <livewire:home.featured-component />
        </section>

        {{-- Services Section --}}
        <section id="services" class="container mx-auto px-1">
            <livewire:home.services-component />
        </section>

        {{-- Gallery Section --}}
        <section id="gallery" class="container mx-auto px-1">
            @include('home.gallery.main')
        </section>

        {{-- Contacts Section --}}
        <section id="contacts" class="container mx-auto px-1 mb-12">
            <livewire:home.contacts-component />
        </section>

        {{-- Footer Section (Included in Main Body) --}}
        <section id="footer-content" class="container mx-auto px-1">
            @include('home.footer.main')
        </section>
    </div>
@endsection
