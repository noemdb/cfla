<div class="modal fade" id="{{ $modal_id ?? '' }}" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog  modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header alert-secondary p-2">
            <h5 class="modal-title" id="exampleModalLabel">Concepto de Cobro <span class=" font-weight-bolder">{{ $cuentaxpagar->name ?? '' }}</span></h5>
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
