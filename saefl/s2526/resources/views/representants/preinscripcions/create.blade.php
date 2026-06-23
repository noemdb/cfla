@extends('representants.layouts.dashboard.app')

@section('main')

    <main role="main">

        <div class="card card-primary mt-2">
            <div class="card-header">
                {{-- INI Menu rapido --}}
                <div class="btn-group float-right pt-2">
                    @include('representants.preinscripcions.menus.create')
                </div>
                {{-- FIN Menu rapido --}}
                <h4>
                    Registrar <b>Preinscripción</b> <small class=" text-muted"> - Período Escolar {{ Session::get('pescolar_name') }}</small>
                </h4>

            </div>

            <div class="card-body">

                @include('representants.elements.forms.errors')

                @include('representants.elements.messeges.oper_ok')

                    <div class="row">

                        <div class="col-sm-9">

                            {!! Form::open(['route' => 'representants.preinscripcions.store', 'method' => 'POST', 'id'=>'form-preinscripcion-create', 'class'=>'form-signin','files'=>'true','enctype'=>"multipart/form-data"]) !!}

                            @include('representants.preinscripcions.form.create')

                            <button type="submit" class="btn-preinscripcion-create btn btn-primary btn-block" value="Registrar" data-id="create" id="btn-create-inscripcion-{{$inscripcion->id ?? ''}}">
                                <i class="far fa-save"></i>
                                Registrar
                            </button>

                            {!! Form::close() !!}

                        </div>

                        <div class="col-sm-3">
                            @includeif('representants.preinscripcions.partials.list')
                        </div>

                    </div>

            </div>
        </div>
    </main>

@endsection

@section('scripts')
    @parent

@endsection
