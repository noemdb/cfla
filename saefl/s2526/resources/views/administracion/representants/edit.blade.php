@extends('administracion.layouts.dashboard.app')

@section('title') - Editar Representante @endsection

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
                Datos del Representante
            </h3>
        </div>

        <div class="card-body">

            @include('administracion.elements.forms.errors')

            @include('administracion.elements.messeges.oper_ok')

            {!! Form::model($representant,['route' => ['administracion.representants.update',$representant->id], 'method' => 'PUT', 'id'=>'formRepresentant','role'=>'form']) !!}

            <div class="card bd-callout bd-callout-primary">
                <h4 class="card-header">
                    <i class="{{ $icon_menus['editar'] }} fa-1x"></i>
                    Actualizar Representante
                </h4>

                {{-- <small class="font-weight-bold text-mute">Período Escolar: {{ Session::get('pescolar_name') }}</small>
                --}}

                <div class="card-body">

                    <div class="row">
                        {{-- <div class="col-3">
                            @include('administracion.representants.deck.card.representant')
                        </div> --}}
                        <div class="col-12">
                            @include('administracion.representants.form.fields')
                        </div>
                    </div>

                </div>
                <button type="submit" class="btn-estudiant-create btn btn-primary btn-block" value="Actualizar"
                    data-id="create" id="btn-create-estudiant-{{$estudiant->id ?? ''}}">
                    <i class="far fa-save"></i>
                    Actualizar
                </button>
            </div>

            {!! Form::close() !!}

        </div>
    </div>
</main>

@endsection

@section('scripts')
@parent

@endsection
