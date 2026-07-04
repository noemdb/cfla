@extends('administracion.layouts.dashboard.app')

@section('main')

    <main role="main" class="col-md-10 ml-sm-auto col-lg-10">

        <div class="card card-primary mt-2">
            <div class="card-header  alert-info">
                {{-- INI Menu rapido --}}
                <div class="btn-group float-right pt-2">
                    {{-- @include('admin.users.menus.index') --}}
                    {{-- <a id="btn_toxls" class="btn-toxls btn btn-success" href="#" role="button" title="Generar XLS con los CAF no aplicados o disponibles">
                        <i class="fas fa-file-excel" aria-hidden="true"></i>
                    </a> --}}
                </div>
                {{-- FIN Menu rapido --}}

                <span class="small text-muted float-right">
                    <i class="fa fa-info-circle text-info" aria-hidden="true"></i>
                    Los Créditos a Favor omitidos no se podrán usar para el Registro de Pagos
                </span>
                <h4 title="Los CAF omitidos no se podrán usar para el Registro de Pagos">
                    <i class="{{ $icon_menus['crud'] ?? null }} fa-1x text-primary"></i>
                    Omisión de <b class=" font-weight-bold">Créditos a Favor</b> no aplaicados
                </h4>
            </div>

            <div class="card-body p-1 m-1">


                @if (!empty($creditoafavors))
                    <table class="table text-left table-sm">
                        <thead class="thead-dark">
                            <tr>
                                <th class=" text-right">Total General</th>
                                <th>{{ $currency_primary->symbol }} {{ f_float($creditoafavors->sum('credito_ammount')) }}</th>
                                <th class=" text-right">Total Monto Cambiario: </th>
                                <th>{{ $currency_secondary->symbol }} {{ f_float($creditoafavors->sum('exchange_ammount')) }}</th>
                            </tr>
                        </thead>
                    </table>
                @endif

                @include('administracion.creditoafavors.partial.omit',['route'=>'administracion.creditoafavors.omit'])

                <div class="container-fluid">
                    <div class="row">
                        <div class="col-sm-{{ ($modeSetUp) ? '7':'12' }}">
                            @include('administracion.creditoafavors.table.omit')
                        </div>
                        @if ($modeSetUp)
                            <div class="col-sm-5">
                                @include('administracion.creditoafavors.setup.omit')
                            </div>
                        @endif
                    </div>
                </div>

            </div>
        </div>
    </main>

@endsection

@section('scripts') @parent <script type="text/javascript"> document.title = 'SAEFL - Omisión de Créditos a favor, listado'; </script> @endsection
