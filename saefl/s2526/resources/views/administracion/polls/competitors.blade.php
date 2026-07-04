@extends('administracion.layouts.dashboard.app')

@section('title')
    Procesos de Consulta - {{ Auth::user()->rol ?? '' }}
@endsection

@section('main')
    <main role="main" class="col-md-10 col-lg-10">

        <div class="card card-primary mt-2">

            <div class="card-header  alert-info">
                <h4>
                    <strong>Participantes en los Proceso de Consulta</strong>
                    <span class="small font-weight-bold">[{{ Auth::user()->username }}]</span>
                </h4>
            </div>

            <div class="card-body">

                {{ $poll_main->name ?? null }}

                <div class="text-muted pl-4"><i>{{ $poll_main->description ?? null }}<i></div>

                <hr>

                @include('administracion.polls.form.search', [
                    'route' => 'administracion.polls.competitors',
                ])

                <hr>

                @php $poll_questions = ($poll_main) ? $poll_main->poll_questions : collect(); @endphp

                {{-- @include('administracion.polls.table.participantes') --}}
                @include('administracion.polls.table.poll_tokens')

            </div>
        </div>
        {{-- <livewire:administracion.poll.index-component /> --}}
    </main>
@endsection
