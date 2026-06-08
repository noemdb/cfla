@extends('bienestars.layouts.dashboard.app')

@section('main')

    <main role="main">

        <div class="p-4">
            @include('bienestars.home.indicators.catchment')
            @include('bienestars.home.indicators.interview')
        </div>

        <div class="mt-1 pt-1">
            @includeif('bienestars.home.about')
        </div>

@endsection


