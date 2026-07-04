@extends('administracion.layouts.dashboard.app')

@section('main')

    <main role="main" class="col-md-10 ml-sm-auto col-lg-10">

        <div class="card card-primary mt-2">
            <div class="card-header  alert-info">
                {{-- INI Menu rapido --}}
                <div class="btn-group float-right pt-2">
                    @include('administracion.abonos.menus.crud')
                </div>
                {{-- FIN Menu rapido --}}

                <h4><span title="Listado especial con botones de acción"><u>Listado</u></span> de Abonos registrados</h4>

            </div>

            <div class="card-body p-1 m-1">

                @if (!empty($abonos))
                    <table class="table text-left table-sm">
                        <thead class="thead-dark">
                            <tr>
                                <th class=" text-right">Total General</th>
                                <th>{{ $currency_primary->symbol }} {{ f_float($abonos->sum('ingreso_ammount')) }}</th>
                                <th class=" text-right">Total Monto Cambiario: </th>
                                <th>{{ $currency_secondary->symbol }} {{ f_float($abonos->sum('exchange_ammount')) }}</th>
                            </tr>
                        </thead>
                    </table>
                @endif

                @include('administracion.abonos.form.search',['route'=>'administracion.abonos.crud','btn_toprint'=>true])

                @include('administracion.abonos.table.crud')

            </div>

        </div>

    </main>

@endsection

@section('title') Abonos, listado - {{ Auth::user()->rol ?? '' }} @endsection

@section('scripts')
    @parent

    <script type="text/javascript">

        $(document).ready(function() {
            $('#btn_toprint').click(function (e) {
                e.preventDefault();
                var banco_id = $('#banco_id').val();	//console.log(ci_estudiant);
                var finicial = $('#finicial').val();	//console.log(ci_estudiant);
                var ffinal = $('#ffinal').val();	//console.log(ci_estudiant);
                var dataString = '?banco_id='+banco_id+'&finicial='+finicial+'&ffinal='+ffinal; console.log(dataString);
                var url = "{{ route('administracion.creditoafavors.libro.pdf') }}"+dataString;
                window.open(url,'_blank');
            });
        });

    </script>

@endsection

