@extends('layouts.miniapp')

@section('title', 'U.E. Colegio Fray Luis Amigó')

@section('header') <livewire:home.header-component  /> @endsection

@section('main') <livewire:app.enrollment.main-component /> @endsection

@section('footer')  @include('payment.footer.main') @endsection

