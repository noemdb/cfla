@extends('evaluacions.layouts.dashboard.app')

@section('main')
    <main role="main">

        <div class="card card-primary mt-2">

            <div class="card-header alert-success">

                <h3 class="mb-0 pb-0 font-weight-bold">
                    <i class="{{ $icon_menus['inicials'] ?? '' }} text-secondary" aria-hidden="true"></i>
                    Educación Inicial, Formatos de Planificación
                </h3>

            </div>

            <div class="card-header p-0 m-0 mb-3">
                {!! Form::open([
                    'route' => 'evaluacions.inicials.index',
                    'method' => 'GET',
                    'class' => 'p-1 m-1',
                    'role' => 'search',
                ]) !!}
                <div class="form-row font-weight-bold">
                    <div class="col-5">Profesor</div>
                    <div class="col-5">Grado</div>
                    <div class="col-2">&nbsp;</div>
                </div>
                <div class="form-row">
                    <div class="col-5">
                        {!! Form::select('profesor_id', $list_profesors, $profesor_id, [
                            'class' => 'form-control',
                            'id' => 'profesor_id',
                            'placeholder' => 'Seleccione',
                        ]) !!}
                    </div>
                    <div class="col-5">
                        {!! Form::select('grado_id', $list_grado, $grado_id, [
                            'class' => 'form-control',
                            'id' => 'grado_id',
                            'placeholder' => 'Seleccione',
                        ]) !!}
                    </div>
                    <div class="col-2">
                        <div class="btn-group w-100">
                            <button class="btn btn-primary btn-block" type="submit">Buscar</button>
                            <a id="" class="btn btn-light" href="{{ url()->current() }}" role="button"
                                title="Refrescar la página">
                                <i class="fas fa-redo" aria-hidden="true"></i>
                            </a>
                        </div>
                    </div>
                </div>
                {!! Form::close() !!}
            </div>

            <div class="card-body p-1 m-1">

                @include('evaluacions.inicilas.stats.main')

                <hr>

                <nav>
                    <div class="nav nav-tabs nav-fill font-weight-bold" id="nav-tab" role="tablist">
                        <a class="nav-item nav-link active" id="nav-format1-tab" data-toggle="tab" href="#nav-format1"
                            role="tab" aria-controls="nav-format1" aria-selected="true">Plan Semanal</a>
                        <a class="nav-item nav-link" id="nav-format2-tab" data-toggle="tab" href="#nav-format2"
                            role="tab" aria-controls="nav-format2" aria-selected="false">Plan Quincenal</a>
                        <a class="nav-item nav-link" id="nav-format3-tab" data-toggle="tab" href="#nav-format3"
                            role="tab" aria-controls="nav-format3" aria-selected="false">Proyecto de Aula</a>
                        <a class="nav-item nav-link" id="nav-format4-tab" data-toggle="tab" href="#nav-format4"
                            role="tab" aria-controls="nav-format4" aria-selected="false">Plan Especial</a>
                        <a class="nav-item nav-link" id="nav-format5-tab" data-toggle="tab" href="#nav-format5"
                            role="tab" aria-controls="nav-format5" aria-selected="false">Plan de Evaluación</a>
                        <a class="nav-item nav-link" id="nav-format6-tab" data-toggle="tab" href="#nav-format6"
                            role="tab" aria-controls="nav-format6" aria-selected="false">Informe Pedagógico</a>
                    </div>
                </nav>
                <div class="tab-content border border-top-0" id="nav-tabContent">
                    <div class="tab-pane fade show active" id="nav-format1" role="tabpanel"
                        aria-labelledby="nav-format1-tab">
                        <div class="py-4 px-2">
                            <livewire:evaluacion.inicial.eiplanningwk-component :profesor_id="$profesor_id" :grado_id="$grado_id" />
                        </div>
                    </div>
                    <div class="tab-pane fade" id="nav-format2" role="tabpanel" aria-labelledby="nav-format2-tab">
                        <div class="py-4 px-2">
                            <livewire:evaluacion.inicial.eiplanningbwk-component :profesor_id="$profesor_id" :grado_id="$grado_id" />
                        </div>
                    </div>
                    <div class="tab-pane fade" id="nav-format3" role="tabpanel" aria-labelledby="nav-format3-tab">
                        <div class="py-4 px-2">
                            <livewire:evaluacion.inicial.eiprojectk-component :profesor_id="$profesor_id" :grado_id="$grado_id" />
                        </div>
                    </div>
                    <div class="tab-pane fade" id="nav-format4" role="tabpanel" aria-labelledby="nav-format4-tab">
                        <div class="py-4 px-2">
                            <livewire:evaluacion.inicial.eispecialk-component :profesor_id="$profesor_id" :grado_id="$grado_id" />
                        </div>
                    </div>
                    <div class="tab-pane fade" id="nav-format5" role="tabpanel" aria-labelledby="nav-format5-tab">
                        <div class="py-4 px-2">
                            <livewire:evaluacion.inicial.eievaluationk-component :profesor_id="$profesor_id" :grado_id="$grado_id" />
                        </div>
                    </div>
                    <div class="tab-pane fade" id="nav-format6" role="tabpanel" aria-labelledby="nav-format6-tab">
                        <div class="py-4 px-2">
                            <livewire:evaluacion.inicial.eifinalk-component :profesor_id="$profesor_id" :grado_id="$grado_id" />
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
                    console.log('event');
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
                    showCancelButton: true,
                    toast: e.detail.toast,
                    position: e.detail.position,
                })
                .then((result) => {
                    if (result.value) {
                        window.livewire.emit(e.detail.method, e.detail.id);
                    }
                });
        });
    </script>
@endsection
