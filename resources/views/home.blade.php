@extends('layouts.home')

@section('title', 'Landing Page')

@section('header')  @include('home.header.main') @endsection

@section('aside')  @include('home.aside.main') @endsection

@section('hero')  @include('home.hero.main') @endsection

@section('highlighted')  @include('home.highlighted.main') @endsection

@section('featured')  @include('home.featured.main') @endsection

@section('category')  @include('home.category.main') @endsection

@section('gallery')  @include('home.gallery.main') @endsection

@section('services')  @include('home.services.main') @endsection

@section('autority')  @include('home.autority.main') @endsection

@section('workers')  @include('home.workers.main') @endsection

{{-- @section('contacs')  @include('home.contacs.main') @endsection --}}

{{-- @section('socials')  @include('home.socials.main') @endsection --}}

@section('testimonials')  @include('home.testimonials.main') @endsection

@section('timeline')  @include('home.timeline.main') @endsection

@section('graphs')  @include('home.graphs.main') @endsection

@section('footer')  @include('home.footer.main') @endsection

{{-- @section('content') <livewire:home /> @endsection --}}