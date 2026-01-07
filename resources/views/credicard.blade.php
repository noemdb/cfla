@extends('layouts.miniapp')

@section('title', 'CENTRO EDUCATIVO')

@section('header') <livewire:home.header-component /> @endsection

{{-- @section('main') @include('home.highlighted.point') @endsection --}}
@section('main') @include('home.highlighted.suspended.point') @endsection

@section('footer') @include('payment.footer.main') @endsection
