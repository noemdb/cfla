@extends('profesors.layouts.dashboard.app')

@section('main')

    <main role="main">

        <div class="card card-primary mt-2 bd-callout bd-callout-gray">

            <div class="card-header alert-secondary">
                <h3 class="mb-0 pb-0 border-bottom">
                    <div class="d-flex justify-content-between">
                        <div><i class="{{$icon_menus['diagnostics'] ?? ''}} text-info pr-1" aria-hidden="true"></i><span class="font-weight-bold">Diagnóstico Educativo</span>. Gestión de preguntas y opciones de respuestas</div>
                    </div>
                </h3>
            </div>

            <div class="card-body p-1 m-1">

                <livewire:profesor.diagnostics.index-component />

            </div>

        </div>

    </main>

@endsection
