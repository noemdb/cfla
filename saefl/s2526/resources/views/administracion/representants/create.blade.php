@extends('administracion.layouts.dashboard.app')

@section('title') - Crear Representante @endsection

@section('main')

<main role="main" class="col-md-10 ml-sm-auto col-lg-10">

    <div class="card card-primary mt-2">
        <div class="card-header">
            {{-- INI Menu rapido --}}
            <div class="btn-group float-right">

                @include('administracion.representants.menus.index')

            </div>
            {{-- FIN Menu rapido --}}
            <h3>
                Nuevo Representante
            </h3>
        </div>

        <div class="card-body">

            @include('administracion.elements.forms.errors')

            @include('administracion.elements.messeges.oper_ok')

            {!! Form::open(['route' => 'administracion.representants.store', 'method' => 'POST', 'id'=>'formRepresentant', 'class'=>'form-signin']) !!}
            {{ Form::hidden('status_active', 'true') }}

            <div class="card bd-callout bd-callout-primary">
                <h4 class="card-header">
                    Datos
                </h4>

                {{-- <small class="font-weight-bold text-mute">Período Escolar: {{ Session::get('pescolar_name') }}</small> --}}

                <div class="card-body">

                    <div class="row">
                        {{-- <div class="col-3">
                            <img class="card-img-top" src="{{ (isset($estudiant->logo)) ? asset($estudiant->logo) : asset('images/avatar/user_default.png') }}" alt="Card image cap">
                        </div> --}}
                        <div class="col-12">
                            @include('administracion.representants.form.fields')
                        </div>
                    </div>

                </div>
            </div>

            <button type="submit" class="btn-inscripcion-create btn btn-primary btn-block" value="Registrar"
                data-id="create" id="btn-create-representants-{{$representants->id ?? ''}}">
                <i class="far fa-save"></i>
                Registrar
            </button>

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
