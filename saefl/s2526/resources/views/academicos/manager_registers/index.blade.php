@extends('academicos.layouts.dashboard.app')

@section('main')

    <main role="main">

        <div class="card card-primary mt-2">

            <div class="card-header  alert-info">

                {{-- <span class="text-muted small font-weight-light float-right" style="font-size: 0.8rem">{{$lapso->name ?? null}}</span> --}}

                <h3 class="mb-0 pb-0 text-uppercase">
                    {{-- <div class="d-flex justify-content-between"> --}}
                        <div>
                            <i class="fa fa-registered text-info" aria-hidden="true"></i>
                            Indicadores para la <span class=" font-weight-bolder">Coordinación de Registro y Control de Estudio</span> por momento de evalaución.
                        </div>
                        <div class="text-right">
                            <span class="text-muted small font-weight-bold">{{$lapso->name ?? null}}</span>
                        </div>
                    {{-- </div>                     --}}
                </h3>

            </div>

            <div class="card-body p-1 m-1">

                <div class="p-2">
                    <div class="h4 text-center pb-0 mb-0">Áreas de Formación, Planes de Evaluación y Carga de Notas por <span class=" font-weight-bolder">Plan de Estudio</span></div>
                    @includeIf('academicos.manager_registers.labels.pensums.main')
                </div>

            </div>
        </div>
    </main>

@endsection
