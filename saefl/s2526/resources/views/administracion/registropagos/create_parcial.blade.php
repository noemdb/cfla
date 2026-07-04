@extends('administracion.layouts.dashboard.app')

@section('main')

<main role="main" id="main" class="col-md-10 ml-sm-auto col-lg-10">

    @php 
        $total_a_pagar = 0;
        $ammount_expire_bill = round($estudiant->ammount_expire_bill,2);
        $ammount_no_expire_bill = round($estudiant->ammount_no_expire_bill,2);
    @endphp

    <div class="card card-primary mt-2">
        <div class="card-header pb-0 mb-0 alert-secondary">
            <small class="float-right text-right small">
                @if ($ammount_expire_bill == 0)
                    <span class="badge badge-success float-right">SOLVENTE</span>
                @else
                    <small class="small text-muted">Total deuda vencida:</small>
                    <span class="badge badge-danger">{{f_float($ammount_expire_bill) ?? null}} Bs.</span>
                @endif
                <br>
                <small class="small text-muted">Total deuda no vencida:</small>
                <span class="badge badge-warning">{{f_float($ammount_no_expire_bill) ?? null}} Bs.</span>
            </small>
            <h4>
                Registro de Pago Parcial<br>
                <span class="small text-muted">
                    {{$estudiant->fullname}} ({{$estudiant->ci_estudiant}})
                </span>
                <span class="small text-dark p-0 m-0">
                    @if (!empty($estudiant))
                      <a title="Histórico de pagos" class="btn btn-sm btn-outline-light "
                          href="{{ route('administracion.representants.historico',['representant_id'=>$estudiant->representant->id]) }}"
                          role="button">
                          <i class="{{ $icon_menus['representante'] ?? ''}} fa-1x text-dark"></i>
                          <i class="{{ $icon_menus['historico'] ?? ''}} fa-1x text-primary"></i>
                      </a>
                    @endif
                </span>
            </h4>
        </div>

        <div class="card-body pt-2">

            @include('administracion.elements.forms.errors')

            @include('administracion.elements.messeges.oper_ok')

            {!!Form::open(['route'=>'administracion.registropagos.parcial.store','method'=>'POST','id'=>'form-registropago-create-estudiant','class'=>'form-signin','novalidate'])!!}

                {{Form::hidden('estudiant_id',$estudiant->id)}}
                {{Form::hidden('representant_id',$estudiant->representant->id)}}

                <div class="row">
                    <div class="col-sm-7 h-100">

                        <h5>Datos para el registro del pago</h5>
                        @include('administracion.registropagos.form.fields.transaccion')

                        <div class="btn-group btn-block" role="group" aria-label="Basic example">
                            <button type="submit" class="submit btn btn-primary p-2 mt-2 w-75" value="Registrar" data-id="create" id="btn-create-registropago">
                                <i class="far fa-save"></i>
                                Registrar
                            </button>
                            <a class="btn btn-outline-info w-25  p-2 mt-2 " href="{{ route('administracion.representants.index',['search'=>$estudiant->representant->ci_representant]) }}" role="button">Otros pagos</a>
                        </div>

                    </div>

                    <div class="col-sm-5">
                        @include('administracion.registropagos.form.parcial.cuentaxpagar')
                        <div class="dropdown-divider p-1 m-1"></div>
                        @include('administracion.registropagos.partial.estudiant.resumen')
                    </div>

                </div>

            {!! Form::close() !!}
        </div>
    </div>
</main>

@endsection
@section('stylesheet')
    @parent

    <style type="text/css">
        .form_fieldset:not(:first-of-type) {
            display: none;
        }
    </style>

@endsection

@section('scripts')
    @parent
    <script type="text/javascript">
    //ini del evento clic

    $(document).ready(function() {
        $('.crt_checkboxes').click(function (e) {
            var div = $(this).parents('div'); //console.log(div); //fila contentiva de la data
            var name = div.data('name');  //console.log(name);
            var checked = $(this).prop('checked'); console.log(checked);
            $('#'+name).val(checked); //console.log($('#'.name).val());
        });
    });

    </script>
@endsection
