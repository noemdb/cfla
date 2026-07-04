@extends('administracion.layouts.dashboard.app')

@section('main')

    <main role="main" class="col-md-10 ml-sm-auto col-lg-10">

        <div class="card card-primary mt-2">
            <div class="card-header  alert-secondary">
                <div class="btn-group float-right pt-0 pb-2">
                    @include('administracion.boletins.menus.ajuste')
                </div>

                <h4>Asignar puntos de ajuste de notas</h4>

            </div>

            <div class="card-body bd-callout bd-callout-{{$grado->color ?? 'default'}} p-2 m-2">

                @include('administracion.boletins.partials.search_ajuste',[
                    'route'=>'administracion.boletins.ajustes',
                    'required_grado'=>true,
                    'required_pensum'=>true,
                    'required_seccion'=>true,
                    'required_lapso'=>true
                    ])

                {{-- @if (!empty($pevaluacion)) --}}
                    {{-- @php $estudiants = (!empty($seccion))  ? $seccion->estudiants_in:null; @endphp --}}
                    {{-- @php $estudiants = $seccion->estudiants_in; @endphp --}}
                    @include('administracion.boletins.table.ajustes')
                {{-- @endif --}}


                {{-- @includewhen($pevaluacion,'administracion.boletins.table.index') --}}

            </div>
        </div>
    </main>

@endsection


