@extends('evaluacions.layouts.dashboard.app')

@section('main')

    <main role="main">

        <div class="card card-primary mt-2">

            <div class="card-header  alert-info">

                <div class="btn-group float-right pt-2">

                </div>

                <h3 class="mb-0 pb-0">
                    <i class="{{$icon_menus['crud'] ?? ''}} text-info" aria-hidden="true"></i>
                    <span class=" font-weight-bold">Listado de Competiciones</span>
                </h3>
                <span class="text-muted small text-capitalize font-light">
                    {{ $autoridad->fullname ?? null}}
                </span>

                <div>
                    Planes de estudio:
                    @foreach ($pestudios as $pestudio)
                        <span class="text-muted pl-2 small">{{$pestudio->name ?? null}}</span>  @if (! $loop->last) || @endif
                    @endforeach
                </div>
            </div>

            <div class="card-body p-1 m-1">

                <livewire:evaluacion.competition.index-component/>

            </div>
        </div>
    </main>

@endsection

