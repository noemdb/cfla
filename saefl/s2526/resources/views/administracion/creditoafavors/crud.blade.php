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
                <h4>
                     <span title="Listado especial con botones de acción"><u>Listado</u></span> de <b class=" font-weight-bold">Créditos a Favor</b> registrados
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

                @include('administracion.creditoafavors.partial.search',[
                    'route'=>'administracion.creditoafavors.crud',
                    'btn_toprint'=>false
                    ])

                @include('administracion.creditoafavors.table.crud')

                {{-- {{ $creditoafavors }} --}}

            </div>
        </div>
    </main>

@endsection

@section('scripts') @parent <script type="text/javascript"> document.title = 'SAEFL - Créditos a favor, listado'; </script> @endsection

@section('scripts')
    @parent

    <script type="text/javascript">

        //btn para ir a editar los datos del EST
        $(document).ready(function() {
          $('#btn_toprint').click(function (e) {
              e.preventDefault();
              var finicial = $('#finicial').val();	//console.log(ci_estudiant);
              var ffinal = $('#ffinal').val();	//console.log(ci_estudiant);
              var ci = $('#ci').val();	//console.log(ci_estudiant);
              var state = $('#state').val();	//console.log(ci_estudiant);
              var dataString = '?finicial='+finicial+'&ffinal='+ffinal+'&ci='+ci+'&state='+state; //console.log(dataString);
              var url = "{{ route('administracion.creditoafavors.libro.pdf') }}"+dataString;
              window.open(url,'_blank');
          });

          $('#btn_toxls').click(function (e) {
              e.preventDefault();
              var url = "{{ route('administracion.creditoafavors.list.credito.dw.excel') }}";
              window.open(url,'_self');
          });
        });

    </script>

@endsection
