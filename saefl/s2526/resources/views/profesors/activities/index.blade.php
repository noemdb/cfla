@extends('profesors.layouts.dashboard.app')

@section('main')

    <main role="main">

        <div class="card card-primary mt-2 bd-callout bd-callout-gray">

            <div class="card-header alert-secondary">
                <h3 class="mb-0 pb-0 border-bottom">
                    <div class="d-flex justify-content-between">
                        <div><i class="{{$icon_menus['activities'] ?? ''}} text-info pr-1" aria-hidden="true"></i><span class="font-weight-bold">Módulo Planificación Académica</span></div>
                        <div><span class="text-muted font-weight-bold" style="font-size: 1rem;opacity: 0.5;">Diseñado por: Prof. Carmin Cortez</span></div>
                    </div>
                </h3>                
            </div>
            
            <div class="card-body p-1 m-1">

                
                @include('profesors.activities.partials.search',['route'=>'profesors.activities.index'])

                <h6 class="pb-2 font-weight-bold text-muted"><u title="Listado especial con botones de acción">Listado</u> de Áreas de Formación <small>[Prof: {{ Auth::user()->profesor->fullname}}]</small></h6>

                @include('profesors.activities.table.index')

            </div>
        </div>
        
    </main>

@endsection

