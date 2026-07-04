@extends('administracion.layouts.dashboard.app')

@section('main')
    <main role="main" class="col-md-10 ml-sm-auto col-lg-10">

        <div class="card card-primary mt-2">
            <div class="card-header  alert-info">
                <h4>Servicios Ejecutados Acción Comunitaria</h4>
            </div>

            {!! Form::open([
                'route' => 'administracion.social_actions.index',
                'method' => 'GET',
                'class' => 'p-2',
                'role' => 'search',
            ]) !!}
            <div class="form-row font-weight-bold">
                <div class="col-8">Grado/Sección</div>
                <div class="col-4">&nbsp;</div>
            </div>
            <div class="form-row">

                <div class="col-8">
                    <div class="btn-group btn-group-sm btn-block" role="group" aria-label="Basic example">
                        {!! Form::select('grado_id', $list_grado, $grado_id, [
                            'class' => 'form-control form-control-sm',
                            'id' => 'grado_id',
                            'placeholder' => 'Seleccione',
                            'required',
                        ]) !!}
                    </div>
                </div>

                <div class="col-4">
                    <div class="btn-group btn-group-sm btn-block" role="group" aria-label="Basic example">
                        <button class="btn btn-primary btn-block" type="submit">
                            <i class="fa fa-search" aria-hidden="true"></i>
                            Buscar
                        </button>
                    </div>
                </div>
            </div>
            {!! Form::close() !!}

            <div class="card-body">

                <nav>
                    <div class="nav nav-tabs nav-fill" id="nav-tab" role="tablist">
                        <a class="nav-item nav-link active" id="nav-home-tab" data-toggle="tab" href="#nav-home"
                            role="tab" aria-controls="nav-home" aria-selected="true">Estudiantes</a>
                        <a class="nav-item nav-link" id="nav-profile-tab" data-toggle="tab" href="#nav-profile"
                            role="tab" aria-controls="nav-profile" aria-selected="false">Profesores</a>
                        <a class="nav-item nav-link" id="nav-service-tab" data-toggle="tab" href="#nav-service"
                            role="tab" aria-controls="nav-service" aria-selected="false">Servicios</a>
                    </div>
                </nav>
                <div class="tab-content" id="nav-tabContent">
                    <div class="tab-pane fade border border-top-0 rounded-bottom p-2 show active" id="nav-home"
                        role="tabpanel" aria-labelledby="nav-home-tab">
                        <div class="p-2">@include('administracion.social_actions.table.estudiants')</div>
                    </div>
                    <div class="tab-pane fade border border-top-0 rounded-bottom p-2" id="nav-profile" role="tabpanel"
                        aria-labelledby="nav-profile-tab">
                        <div class="p-2">@include('administracion.social_actions.table.profesors')</div>
                    </div>
                    <div class="tab-pane fade border border-top-0 rounded-bottom p-2" id="nav-service" role="tabpanel"
                        aria-labelledby="nav-service-tab">
                        <div class="p-2">
                            <livewire:administracion.social-action.index-component :grado_id="$grado_id" />
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </main>
@endsection

@section('sweetalert')
    @parent
    <script>
        window.addEventListener('swal', function(e) {
            Swal.fire(e.detail);
        });

        window.addEventListener('swal:confirm', function(e) {

            Swal.fire({
                    title: e.detail.message,
                    text: e.detail.text,
                    icon: e.detail.type,
                    buttons: true,
                    dangerMode: true,
                    showCancelButton: true,
                })
                .then((result) => {
                    if (result.value) {
                        window.livewire.emit('remove', e.detail.id);
                    }
                });
        });

        window.addEventListener('swal:question', function(e) {
            Swal.fire({
                    title: e.detail.message,
                    text: e.detail.text,
                    icon: e.detail.type,
                    buttons: true,
                    dangerMode: true,
                    showCancelButton: true,
                })
                .then((result) => {
                    if (result.value) {
                        window.livewire.emit(e.detail.method, e.detail.id);
                    }
                });
        });
    </script>
@endsection
