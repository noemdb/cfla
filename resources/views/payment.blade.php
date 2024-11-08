@extends('layouts.miniapp')

@section('title', 'C.E. Colegio Fray Luis Amigó')

@section('header') <livewire:home.header-component  /> @endsection

@section('main') <livewire:app.payment.index-component /> @endsection

@section('footer')  @include('payment.footer.main') @endsection

