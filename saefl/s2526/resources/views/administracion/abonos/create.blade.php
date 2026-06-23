@extends('administracion.layouts.dashboard.app')

@section('title') - Registro de Abonos @endsection

@section('main')

@php
    $representant = $estudiant->representant;
@endphp

<main role="main" id="main" class="col-md-10 ml-sm-auto col-lg-10">
    <div class="card card-primary mt-2">
        <div class="card-header pb-0 mb-0 alert-secondary">
            <h3>
                Datos para el registro del Abono<br>
                <small class="text-default">
                    {{-- <strong><span id="user_counter">{{$users->count()}}</span> Usuarios</strong> --}}
                </small>

                {{-- INI Menu rapido --}}
                <div class="btn-group float-right pt-2">

                    {{-- @include('administracion.configuraciones.menus.index') --}}

                </div>
                {{-- FIN Menu rapido --}}

            </h3>
        </div>

        <div class="card-body pt-2">

            @include('administracion.elements.forms.errors')

            @include('administracion.elements.messeges.oper_ok')

            {!!Form::open(['route'=>'administracion.abonos.store','method'=>'POST','id'=>'form-abonos-create','class'=>'form-signin'])!!}

            {{Form::hidden('estudiant_id',$estudiant->id)}}
            {{Form::hidden('representant_id',$representant->id)}}

            {{-- <small class="font-weight-bold text-mute">Período Escolar: {{ Session::get('pescolar_name') }}</small>
            --}}

            <div class="row">
                <div class="col-2 p-0">
                    @include('administracion.registropagos.deck.card.estudiant_simple')
                </div>
                <div class="col-10 h-100">
                    <div
                        class="card p-0">
                        <div class="card-header pb-0 mb-0" >
                        <h6>
                            Estudiante: {{$estudiant->fullname}}
                            <br>
                            <small class="pl-3">
                                Representante: {{$representant->name}}
                            </small>
                        </h6>
                        </div>
                        <div class="card-body p-3">
                            @include('administracion.abonos.form.fieldsExchage')

                            <div class="btn-group btn-block" role="group" aria-label="Basic example">
                                <button type="submit" class="btn btn-primary w-50">
                                    <i class="far fa-save"></i>
                                    Guardar y continuar
                                </button>
                                <a class="btn btn-success w-25" href="{{ route('administracion.registropagos.asistent.representant.create',$representant->id) }}" role="button">Iniciar asistente de pago</a>
                                <a class="btn btn-dark w-25" href="{{ route('administracion.abonos.index') }}" role="button">Finalizar</a>
                            </div>

                        </div>
                    </div>
                </div>
                {{-- <div class="col-3 text-right small pl-0"> --}}
                    {{-- @include('administracion.abonos.partial.resumen') --}}
                {{-- </div> --}}
            </div>
            {!! Form::close() !!}
        </div>
    </div>
</main>

@endsection

@section('scripts')
@parent
<script type="text/javascript">
    $(document).ready(function() {
            $('.crt_checkboxes').click(function (e) {
                var div = $(this).parents('div'); //console.log(div); //fila contentiva de la data
                var name = div.data('name');  console.log(name);
                var checked = $(this).prop('checked'); console.log(checked);
                $('#'+name).val(checked); console.log($('#'.name).val());
            });
        });

        $(document).ready(function(){
            $("#cuentaxpagar_id").change(function(){
                var cuentaxpagar_id = $(this).val();console.log(cuentaxpagar_id);
                var url = "{{ route('administracion.abonos.create',[$estudiant->id,'']) }}/"+cuentaxpagar_id;
                window.open(url,'_self')
            });
        });
</script>
@endsection
