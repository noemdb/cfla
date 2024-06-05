@extends('general.educational.competition.board.layouts.home')

@section('title', 'C.E. Colegio Fray Luis Amigó - Competiciones')

@section('header') @include('general.educational.competition.board.header') @endsection

@section('main') <livewire:app.general.educational.competition.dashboard.index-component :token="$token"/> @endsection

@section('footer') @include('general.educational.competition.board.footer') @endsection