@extends('administracion.layouts.dashboard.app')

@section('main')

    <main role="main" class="col-md-10 ml-sm-auto col-lg-10">

        <div class="card card-primary mt-2">
            <div class="card-header  alert-info">
                {{-- INI Menu rapido --}}
                <div class="btn-group float-right pt-0 pb-2">
                    {{-- @include('administracion.ingresos.menus.crud') --}}
                </div>
                {{-- FIN Menu rapido --}}
                <h4>
                    <span title="Listado especial con botones de acción"><u>Listado</u></span> de ingresos registrados
                </h4>
            </div>

            <div class="card-body p-1 m-1">

                @if (!empty($ingresos))
                    <table class="table text-left table-sm">
                        <thead class="thead-dark">
                            <tr>
                                <th class=" text-right">Total General</th>
                                <th>{{ $currency_primary->symbol }} {{ f_float($ingresos->sum('ingreso_ammount')) }}</th>
                                <th class=" text-right">Total Monto Cambiario: </th>
                                <th>{{ $currency_secondary->symbol }} {{ f_float($ingresos->sum('exchange_ammount')) }}</th>
                            </tr>
                        </thead>
                    </table>
                @endif

                @include('administracion.ingresos.partial.search',['route'=>'administracion.ingresos.crud','btn_toprint'=>true])

                @include('administracion.ingresos.table.crud')

            </div>
        </div>
    </main>

@endsection

@section('title') Ingresos, listado - {{ Auth::user()->rol ?? '' }} @endsection

{{-- @section('scripts') @parent <script type="text/javascript"> document.title = 'SAEFL - Ingresos, listado'; </script> @endsection --}}

@section('scripts')
    @parent

    <script type="text/javascript">


        $(document).ready(function() {
          $('#btn_toprint').click(function (e) {
              e.preventDefault();
              var banco_id = $('#banco_id').val();	//console.log(banco_id);
              var finicial = $('#finicial').val();	//console.log(finicial);
              var ffinal = $('#ffinal').val();	//console.log(ffinal);
              var ci = $('#ci').val();	//console.log(ci);
              var number_i_pay = $('#number_i_pay').val();	//console.log(number_i_pay);
              var dataString = '?banco_id='+banco_id+'&finicial='+finicial+'&ffinal='+ffinal+'&ci='+ci+'&number_i_pay='+number_i_pay; //console.log(dataString);
              var url = "{{ route('administracion.ingresos.movimeintos.pdf') }}"+dataString;
              window.open(url,'_blank');
          });
        });
    </script>

@endsection

