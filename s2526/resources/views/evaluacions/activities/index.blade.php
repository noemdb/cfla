@extends('evaluacions.layouts.dashboard.app')

@section('main')

    <main role="main">

        <div class="card card-primary mt-2 bd-callout bd-callout-info">

            <div class="card-header alert-info">
                <h4 class="mb-0 pb-0 border-bottom">
                    <div class="d-flex justify-content-between">
                        <div><i class="{{$icon_menus['activities'] ?? ''}} text-info pr-1" aria-hidden="true"></i><span class="font-weight-bold">Módulo Plan de Actividades</span></div>
                        <div><span class="text-muted font-weight-bold" style="font-size: 1rem;opacity: 0.5;">Diseñado por: Prof. Carmin Cortez</span></div>
                    </div>

                    @foreach ($pestudios as $pestudio)
                        <span class="text-muted pl-2 small">{{$pestudio->name ?? null}}</span>  @if (! $loop->last) || @endif
                    @endforeach
                </h4>                 
            </div>

            <div class="card-body p-1 m-1">

                <livewire:evaluacion.activity.index-component/>

            </div>

        </div>

    </main>

@endsection

