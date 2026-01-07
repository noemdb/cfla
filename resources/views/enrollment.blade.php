@extends('layouts.miniapp')

@section('title', 'CENTRO EDUCATIVO')

@section('header') <livewire:home.header-component /> @endsection

@section('main') <livewire:app.enrollment.main-component /> @endsection

@section('footer') @include('payment.footer.main') @endsection
