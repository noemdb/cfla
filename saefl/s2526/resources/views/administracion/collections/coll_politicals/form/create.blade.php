{!! Form::open(['route' => 'administracion.collections.coll_politicals.store', 'method' => 'POST', 'id'=>'form-collPolitical-create', 'class'=>'form-signin']) !!}

        <div class="card bd-callout bd-callout-primary">

            <h5 class="card-header pb-1 mb-1">
                <i class="{{ $icon_menus['nuevo'] }} fa-1x text-primary float-right"></i>
                Datos
            </h5>

            <div class="card-body p-2">

                <div class="row">
                    <div class="col">

                        @include('administracion.collections.coll_politicals.form.fields')

                        <button type="submit" class="btn-user-create btn btn-primary btn-block" value="create" data-id="create" id="btn-create">
                            <i class="far fa-save"></i>
                            Registrar
                        </button>
                    </div>
                    <div class="col-3">
                        @include('administracion.collections.coll_politicals.partials.resumen.create')
                    </div>
                </div>

            </div>
        </div>

    {!! Form::close() !!}
