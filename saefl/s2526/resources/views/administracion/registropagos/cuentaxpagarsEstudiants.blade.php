@extends('administracion.layouts.dashboard.app')

@section('main')

    <main role="main" class="col-md-10 ml-sm-auto col-lg-10">

        <div class="card card-primary mt-2">

            <div class="card-header alert-secondary">

                {{-- INI Menu rapido --}}
                <div class="btn-group float-right pt-2">
                    @include('administracion.registropagos.menus.crud')
                </div>
                {{-- FIN Menu rapido --}}
                <h4><span title="Listado especial con botones de acción"><u>Listado</u></span> de <span class="font-weight-bolder">Estudiantes con conceptos de cobro individuales pendientes</span></h4>
            </div>

            <div class="card-body p-2">

                <div class="card-header p-0 m-0 mb-2">
                    {!! Form::open(['route'=>'administracion.registropagos.cuentaxpagars.estudiants','method'=>'GET','class'=>'pb-1','id'=>'form_search','role'=>'search']) !!}

                        @include('administracion.registropagos.form.search.cuentaxpagarsEstudiants')

                    {!! Form::close() !!}
                </div>

                {{--
                @if($monto_exchange_total)
                    <div class=" bg-light pb-1 font-weight-bold d-block alert-dark">
                        <div class="float-right d-block">
                            Total General: <span>$ {{f_float($monto_exchange_total) ?? ''}}</span>
                            Nº <span>{{ $datas->count() ?? ''}}</span>
                        </div>
                    </div>
                @endif

                --}}

                <br>

                {{-- {{ $representants ?? '' }} --}}

                @include('administracion.registropagos.table.cuentaxpagarsEstudiants')


            </div>
        </div>
    </main>

@endsection

@section('scripts')
    @parent

    <script type="text/javascript">
        //btn para ir a editar los datos del EST
        $(document).ready(function() {
          $('#btn_xls').click(function (e) {
              e.preventDefault();
              var cuentaxpagar_id   = $('#cuentaxpagar_id').val();	//console.log(ci_estudiant);
              var ci_representant   = $('#ci_representant').val();	//console.log(ci_estudiant);
              var dataString = '?cuentaxpagar_id='+cuentaxpagar_id+'&ci_representant='+ci_representant; //console.log(dataString);
              var url = "{{ route('administracion.registropagos.export.representants.cuentaxpagars.pendientes') }}"+dataString; //console.log(url);
              window.open(url,'_blank');
          });
        });
    </script>

@endsection
