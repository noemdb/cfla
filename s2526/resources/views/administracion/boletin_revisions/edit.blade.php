@extends('administracion.layouts.dashboard.app')

@section('main')

    <main role="main" class="col-md-10 ml-sm-auto col-lg-10">

        <div class="card card-primary mt-2">
            <div class="card-header alert-secondary">
                <h3>
                    {{-- INI Menu rapido --}}
                    <div class="btn-group float-right pt-2">
                        @include('administracion.boletin_revisions.menus.edit')
                    </div>
                    {{-- FIN Menu rapido --}}

                    Actualizar Revisión

                </h3>
            </div>

            <div class="card-body">

                    @include('administracion.elements.forms.errors')

                    @include('administracion.elements.messeges.oper_ok')

                    {!! Form::model($boletin_revision,['route' => ['administracion.boletin_revisions.update', $boletin_revision->id], 'method' => 'PUT', 'id'=>'form-update-boletin_'.$boletin_revision->id, 'role'=>'form']) !!}

                        <div class="card bd-callout bd-callout-primary">
                            <h4 class="card-header">
                                <i class="{{ $icon_menus['editar'] }} fa-1x"></i>
                                Datos de la Revisión
                            </h4>

                            <div class="card-body">

                                <div class="row">
                                    <div class="col-2">
                                        @include('administracion.boletin_revisions.cards.estudiant')
                                    </div>
                                    <div class="col-8">
                                        @include('administracion.boletin_revisions.form.fields')
                                        <button type="submit" class="btn-boletin-edit btn btn-primary btn-block" value="Actualizar" data-id="edit" id="btn-edit-boletin-{{$boletin_revision->id ?? ''}}">
                                            <i class="far fa-save"></i>
                                            Actualizar
                                        </button>
                                    </div>

                                    <div class="col-2 text-muted">
                                        @include('administracion.boletin_revisions.partials.resumen.pensums')
                                    </div>

                                </div>

                                <hr>
                                <div class="card">
                                    <div class="card-body">
                                        <h5 class="card-title text-primary">Listado de <span class=" font-weight-bold"> Revisiones </span> registradas</h5>
                                        <p class="card-text">
                                            @php $boletin_revisions = $estudiant->boletin_revisions @endphp
                                            @include('administracion.boletin_revisions.partials.crud')
                                        </p>
                                    </div>
                                </div>

                            </div>
                        </div>

                    {!! Form::close() !!}

            </div>
        </div>
    </main>

@endsection

@section('scripts')
    @parent

@endsection
