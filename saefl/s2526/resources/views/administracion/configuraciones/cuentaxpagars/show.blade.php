@extends('administracion.layouts.dashboard.app')

@section('main')

    <main role="main" class="col-md-10 ml-sm-auto col-lg-10">

        <div class="card card-primary mt-2">
            <div class="card-header alert-secondary">
                <h4>Mostrar detalles concepto de cobro <span class=" font-weight-bolder">{{$cuentaxpagar->name ?? ''}}</span></h4>
            </div>

            <div class="card-body">

                @include('administracion.elements.forms.errors')

                @include('administracion.elements.messeges.oper_ok')

                <div class="card bd-callout bd-callout-{{$cuentaxpagar->status_active=='true'  ? 'primary':'danger'}}">
                    <h5 class="card-header">Datos</h5>
                    <div class="card-body">
                        <div class="container">
                            <div class="row">
                                <div class="col-sm-8">
                                    {!! Form::model($cuentaxpagar,['route' => ['administracion.configuraciones.cuentaxpagars.update', $cuentaxpagar->id], 'method' => 'PUT', 'id'=>'form-update', 'role'=>'form']) !!}
                                        <fieldset id="fieldset">
                                        @include('administracion.configuraciones.cuentaxpagars.form.field',$cuentaxpagar)
                                        <button type="submit" class="btn-user-update btn btn-primary btn-block" value="update" data-id="update" id="btn-update-cuentaxpagar-{{$cuentaxpagar->id ?? ''}}">
                                            <i class="far fa-save"></i>
                                            Actualizar
                                        </button>
                                        </fieldset>
                                    {!! Form::close() !!}
                                </div>
                                <div class="col-sm-4">
                                    @include('administracion.configuraciones.cuentaxpagars.partial.concepto',$cuentaxpagar)
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </main>

@endsection

@section('scripts')
    @parent
    <script type="text/javascript">
        $(document).ready(function(){
            if ({{ ($cuentaxpagar->status_edit) ? 0:1 }}) {
                $("#fieldset").attr("disabled", "disabled");
                $('input[name$="_token"]').remove();
            }
        });
    </script>

    {{-- INI script ajax json models --}}
    {{-- <script src="{{ asset("js/models/users/delete.js") }}"></script> --}}
    {{-- FIN script ajax json models --}}

@endsection

@section('style')
    @parent
@endsection




