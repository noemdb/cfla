<div class="modal fade" id="{{ $modal_id ?? '' }}" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog  modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header alert-secondary p-2">
            <h5 class="modal-title" id="exampleModalLabel">Asignar Cuenta al Concepto <span class=" font-weight-bolder">{{ $cuentaxpagar->name ?? '' }}</span></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">

                <div class="row">
                    <div class="col-8">
                        <div class="card bd-callout bd-callout-primary">
                            <h5 class="card-header pb-0 mb-0">
                                <span class="font-weight-bold text-primary">Datos</span>
                            </h5>
                            <div class="p-2">
                                {!! Form::open(['route' => 'administracion.configuraciones.concepto_pagos.store_from_cuentaxpagar', 'method' => 'POST', 'id'=>'form-concepto_pagos-create', 'class'=>'form-signin']) !!}
                                    {{ Form::hidden('cuentaxpagar_id', $cuentaxpagar->id) }}
                                    @include('administracion.configuraciones.concepto_pagos.form.field')
                                    <button type="submit" class="btn-user-update btn btn-primary btn-block" value="update" data-id="update" id="btn-update-cuentaxpagar-{{$cuentaxpagar->id ?? ''}}">
                                        <i class="far fa-save"></i>
                                        Registrar
                                    </button>
                                {!! Form::close() !!}
                            </div>
                        </div>
                    </div>
                    <div class="col-4">
                        @include('administracion.configuraciones.cuentaxpagars.partial.concepto')
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
