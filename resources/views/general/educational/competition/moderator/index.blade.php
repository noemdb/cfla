@extends('general.educational.competition.moderator.layouts.home')

@section('title', 'C.E. Colegio Fray Luis Amigó - Competiciones - Moderador')

@section('header') @include('general.educational.competition.moderator.header') @endsection

@section('main') <livewire:app.general.educational.competition.moderator.index-component :token="$token"/> @endsection

@section('footer') @include('general.educational.competition.moderator.footer') @endsection

