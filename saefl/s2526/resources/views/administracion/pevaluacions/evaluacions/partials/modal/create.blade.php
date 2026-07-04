<div class="content">
    <div class="row">

        <div class="col-sm-3">
            <h5 class="card-title">Resumen</h3>
            <div class="dropdown-divider mb-0"></div>
            @include('administracion.pevaluacions.partials.resumen')
        </div>

        <div class="col-sm-6">

            <div class="card shadow p-1 mb-5 bg-white rounded">
                <div class="card-body p-1 m-1">
                    <h6 class="card-title"> <b>Registro de Evaluaciones</b></h6>
                    <div class="dropdown-divider mb-0"></div>
                    @php $id = (!empty($pevaluacion->id)) ? ''.$pevaluacion->id : null ; @endphp
                    {!! Form::open(['route' => 'administracion.evaluacions.store', 'method' => 'POST', 'id'=>'form-evaluacions-create_'.$id, 'class'=>'form-signin']) !!}

                    @include('administracion.pevaluacions.evaluacions.form.fields')

                    <div class="btn-group btn-block" role="group" aria-label="Basic example">

                        <a class="btn-evaluacion-create btn btn-info" href="#" role="button">
                            <i class="fa fa-arrow-right" aria-hidden="true"></i>
                            Continuar
                        </a>
                        <a class="btn btn-primary" href="#" role="button" data-dismiss="modal" aria-label="Close">
                            {{-- <i class="far fa-save"></i> --}}
                            Finalizar
                        </a>
                    </div>
                    {!! Form::close() !!}
                </div>
            </div>

        </div>

        @if (!empty($pevaluacion->evaluacions->first()))
            <div class="col-sm-3">
                <div class="h-100 rounded p-1 m-1 alert-{{ ($pevaluacion->status_eva_complete) ? 'success':'warning' }}">
                    <h5 class="card-title">Lista de Evaluaciones registradas</h3>
                    <div class="dropdown-divider mb-0"></div>
                    @php $evaluacions = (!empty($pevaluacion->evaluacions)) ? $pevaluacion->evaluacions:null; @endphp
                    @includewhen(($evaluacions),'administracion.pevaluacions.partials.evaluacion')
                </div>
            </div>
        @endif

    </div>
</div>
