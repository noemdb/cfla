@extends('general.educational.competition.scoreboard.layouts.home')

@section('title', 'C.E. Colegio Fray Luis Amigó - Competiciones - Pizarra de Resultados')

@section('header') @include('general.educational.competition.scoreboard.header') @endsection

@section('main') <livewire:app.general.educational.competition.scoreboard.index-component :token="$token"/> @endsection

{{-- @section('main') @include('general.educational.competition.scoreboard.main') @endsection --}}

@section('footer') @include('general.educational.competition.scoreboard.footer') @endsection

