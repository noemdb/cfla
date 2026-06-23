<div class="modal fade" id="{{ $modal_id ?? '' }}" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog  modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header alert-secondary p-2">
            <h5 class="modal-title" id="exampleModalLabel">Cuenta de Cobro <span class=" font-weight-bolder">{{ $concepto_pago->name ?? '' }}</span></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">

                <div class="row">
                    <div class="col-8">
                        <fieldset id="fieldset" disabled>
                            @include('administracion.configuraciones.concepto_pagos.show.details')
                        </fieldset>
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
