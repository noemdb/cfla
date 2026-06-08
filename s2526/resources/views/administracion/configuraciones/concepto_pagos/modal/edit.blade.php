<div class="modal fade" id="{{ $modal_id ?? '' }}" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog  modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header alert-secondary p-2">
            <h5 class="modal-title" id="exampleModalLabel">Cuenta de Cobro <span class=" font-weight-bolder">{{ $concepto_pago->nom_concepto_pago->name ?? '' }}</span></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">

                <div class="row">
                    <div class="col-8">
                        <div class="card bd-callout bd-callout-primary">
                            <h5 class="card-header pb-0 mb-0">
                                <span class="font-weight-bold">Datos</span>
                            </h5>
                            <div class="p-2">
                                {!! Form::model($concepto_pago,['route' => ['administracion.configuraciones.concepto_pagos.update', $concepto_pago->id], 'method' => 'PUT', 'id'=>'form-update', 'role'=>'form']) !!}
                                    {{ Form::hidden('cuentaxpagar_id', $concepto_pago->cuentaxpagar_id) }}
                                    @include('administracion.configuraciones.concepto_pagos.form.field',$concepto_pago)
                                    <button type="submit" class="btn-user-update btn btn-primary btn-block" value="update" data-id="update" id="btn-update-concepto_pago-{{$concepto_pago->id ?? ''}}">
                                        <i class="far fa-save"></i>
                                        Actualizar
                                    </button>
                                {!! Form::close() !!}
                            </div>
                        </div>
                    </div>
                    <div class="col-4">
                        @php $cuentaxpagar = (!empty($concepto_pago->cuentaxpagar)) ? $concepto_pago->cuentaxpagar: null; @endphp
                        @include('administracion.configuraciones.concepto_pagos.partials.cuenta')
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
