@extends('administracion.layouts.dashboard.app')

@section('main')

    <main role="main" class="col-md-10 ml-sm-auto col-lg-10">

        <div class="card card-primary mt-2">
            <div class="card-header alert-secondary">
                {{-- INI Menu rapido --}}
                <div class="btn-group float-right pt-2">

                    @include('administracion.boletin_revisions.menus.create')

                </div>
                {{-- FIN Menu rapido --}}
                <h4>
                    Registrar Revisión de Notas
                    {{-- <br>
                    <small class="text-default">
                        <strong><span id="user_counter">{{$users->count()}}</span> Usuarios</strong>
                    </small>                    --}}

                </h4>
            </div>

            <div class="card-body">

                    @include('administracion.elements.forms.errors')

                    @include('administracion.elements.messeges.oper_ok')

                    {!! Form::open(['route' => 'administracion.boletin_revisions.store', 'method' => 'POST', 'id'=>'form-boletin-revision-create', 'class'=>'form-signin']) !!}

                        {{ Form::hidden('estudiant_id', $estudiant->id) }}

                        <div class="card bd-callout bd-callout-primary">
                            <h5 class="card-header">
                                Datos
                            </h5>

                            <div class="card-body">

                                <div class="row">
                                    <div class="col-2">
                                        @include('administracion.boletin_revisions.cards.estudiant')
                                    </div>
                                    <div class="col-8 alert-secondary border rounded p-1">
                                        @include('administracion.boletin_revisions.form.fields')
                                        <button type="submit" class="btn-inscripcion-create btn btn-primary btn-block" value="Registrar" data-id="create" id="btn-create-inscripcion">
                                            <i class="far fa-save"></i>
                                            Registrar
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





@section('scripts')
    @parent

@endsection
