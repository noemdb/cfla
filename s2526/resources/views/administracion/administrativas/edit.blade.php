@extends('administracion.layouts.dashboard.app')

@section('main')

    <main role="main" class="col-md-10 ml-sm-auto col-lg-10">

        <div class="card card-primary mt-2">
            <div class="card-header alert-secondary">
                <h3>
                    {{-- INI Menu rapido --}}
                    <div class="btn-group float-right pt-2">
                        @include('administracion.administrativas.menus.edit')
                    </div>
                    {{-- FIN Menu rapido --}}

                    Actualizar Plan de Pago

                </h3>
            </div>

            <div class="card-body">

                    @include('administracion.elements.forms.errors')

                    @include('administracion.elements.messeges.oper_ok')

                    {!! Form::model($administrativa,['route' => ['administracion.administrativas.update', $administrativa->id], 'method' => 'PUT', 'id'=>'form-update-administrativa_'.$administrativa->id, 'role'=>'form']) !!}

                        <div class="card p-1">
                            <h4 class="card-header">
                                <i class="{{ $icon_menus['editar'] }} fa-1x"></i>
                                Datos
                            </h4>

                            <div class="card-body">

                                <div class="row">
                                    <div class="col-3">
                                        @php
                                            $estudiant = $administrativa->estudiant;
                                        @endphp
                                        @include('administracion.estudiants.deck.card.estudiant_simple')
                                        {{-- @include('administracion.estudiants.deck.card.estudiant') --}}
                                    </div>
                                    <div class="col-6">
                                        @include('administracion.administrativas.form.fields')
                                        <button type="submit" class="btn-administrativa-create btn btn-primary btn-block" value="Actualizar" data-id="create" id="btn-create-administrativa-{{$administrativa->id ?? ''}}">
                                            <i class="far fa-save"></i>
                                            Actualizar
                                        </button>
                                    </div>
                                    <div class="col-3">
                                        @include('administracion.administrativas.partial.estudiant.resume')
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
