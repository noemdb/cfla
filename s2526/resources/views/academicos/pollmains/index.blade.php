@extends('academicos.layouts.dashboard.app')

@section('main')

    <main role="main">

        <div class="card card-primary mt-2">

            <div class="card-header  alert-success">

                <h3 class="mb-0 pb-0">
                    <i class="{{$icon_menus['pollmain'] ?? ''}} text-info" aria-hidden="true"></i>
                    Procesos de Consultas
                </h3>
            </div>

            <div class="card-body p-1 m-1">

                @include('academicos.pollmains.form.search',['route'=>'academicos.pollmains.index'])

                @if ($poll_main)

                    <div class="p-4 h4">
                        {{ $poll_main->name ?? null }} <span class="text-muted">: <i>{{ $poll_main->description ?? null }}<i></span>
                    </div>

                    @include('academicos.pollmains.indicators.main')

                @endif

            </div>
        </div>
    </main>

@endsection

