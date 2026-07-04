@extends('administracion.layouts.dashboard.app')

@section('title') Lapsos/Censo, listado participantes @endsection

@section('main')

    <main role="main" class="col-md-10 ml-sm-auto col-lg-10">

        <div class="card card-primary mt-2">
            <div class="card-header pb-0 mb-0 alert-secondary">
                {{-- INI Menu rapido --}}
                <div class="btn-group float-right pt-0 pb-2">
                    @include('administracion.configuraciones.lapsos.menus.census')
                </div>
                {{-- FIN Menu rapido --}}
                <h4>
                    <u title="Listado especial con botones de acción">Listado</u> de <span class="font-weight-bolder">Participantes registrados en el Censo Escolar</span>
                    <span class="small font-weight-bold">[{{$lapso->name ?? null}}]</span>
                </h4>
            </div>

            <div class="card-body">

                @if($censuses->count())
                    <div class="pl-2 small mb-2">
                        {{-- <div class=" font-weight-bold">{{$lapso->name ?? null}}</div> --}}
                        <span>Fecha de Inicio: {{ f_date($lapso->date_start_census) ?? '[]' }}</span> ||
                        <span>Fecha de Finalización: {{ f_date($lapso->date_end_census) ?? '[]' }}</span> ||
                        <span>Horario {{$lapso->time_start_census ?? '[]'}} hasta las {{$lapso->time_end_census ?? '[]'}}</span>
                        <i>Sólo días hábiles (Lun. a Vie.)</i>
                    </div>
                @endif

                @include('administracion.elements.messeges.oper_ok')

                @include('administracion.configuraciones.lapsos.table.census')

            </div>
        </div>
    </main>

@endsection
