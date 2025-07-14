@extends('layouts.miniapp')

@section('title', 'C.E. Colegio Fray Luis Amigó')

@section('header') <livewire:home.header-component  /> @endsection

{{-- @section('main') @include('home.highlighted.point') @endsection --}}
@section('main') @include('home.highlighted.suspended.point') @endsection

@section('footer')  @include('payment.footer.main') @endsection

