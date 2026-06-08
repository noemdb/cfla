@extends('administracion.layouts.dashboard.app')

@section('main')

    <main role="main">

        <div class="card card-primary mt-2">
            <div class="card-header alert-secondary">
                <h3>
                    {{-- INI Menu rapido --}}
                    <div class="btn-group float-right pt-2">
                        @include('administracion.boletins.menus.edit')
                    </div>
                    {{-- FIN Menu rapido --}}

                    Actualizar boletin

                </h3>
            </div>

            <div class="card-body">

                    @include('administracion.elements.forms.errors')

                    @include('administracion.elements.messeges.oper_ok')

                    {!! Form::model($boletin,['route' => ['administracion.boletins.update', $boletin->id], 'method' => 'PUT', 'id'=>'form-update-boletin_'.$boletin->id, 'role'=>'form']) !!}

                        <div class="card bd-callout bd-callout-primary">
                            <h4 class="card-header">
                                <i class="{{ $icon_menus['editar'] }} fa-1x"></i>
                                Datos del boletin
                            </h4>

                            <div class="card-body">

                                <div class="row">
                                    <div class="col-3">
                                        {{-- @include('administracion.boletins.deck.card.estudiant_simple') --}}
                                    </div>
                                    <div class="col-9">
                                        @include('administracion.boletins.form.fields')
                                        <button type="submit" class="btn-boletin-create btn btn-primary btn-block" value="Actualizar" data-id="create" id="btn-create-boletin-{{$boletin->id ?? ''}}">
                                            <i class="far fa-save"></i>
                                            Actualizar
                                        </button>
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
