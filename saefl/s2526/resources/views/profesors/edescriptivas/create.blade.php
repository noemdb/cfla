@extends('profesors.layouts.dashboard.app')

@section('main')

    <main role="main" class="col-md-10 ml-sm-auto col-lg-10">

        <div class="card card-primary mt-2">
            <div class="card-header alert-secondary">
                {{-- INI Menu rapido --}}
                <div class="btn-group float-right pt-2">

                    {{-- @include('profesors.configuraciones.menus.index') --}}

                </div>
                {{-- FIN Menu rapido --}}
                <h4>
                    Registrar nueva <span class=" font-weight-bold">Evaluacion Descriptiva</span><br>


                </h4>
            </div>

            <div class="card-body">

                    @include('profesors.elements.forms.errors')

                    @include('profesors.elements.messeges.oper_ok')

                    {!! Form::open(['route' => 'profesors.edescriptivas.store', 'method' => 'POST', 'id'=>'form-edescriptivas-create', 'class'=>'form-signin']) !!}

                        {{-- {{ Form::hidden('pescolar_id', Session::get('pescolar_id')) }} --}}

                        <div class="card bd-callout bd-callout-primary">
                            <h5 class="card-header">
                                Datos de la <span class=" font-weight-bold">Evaluacion Descriptiva</span>
                            </h5>

                            <div class="card-body">

                                <div class="row">
                                    <div class="col-9">

                                        @include('profesors.edescriptivas.form.fields')

                                        <button type="submit" class="btn-edescriptivas-create btn btn-primary btn-block" value="Registrar" data-id="create" id="btn-create-edescriptivas">
                                            <i class="far fa-save"></i>
                                            Registrar
                                        </button>

                                    </div>
                                    <div class="col-3">
                                        {{-- @include('profesors.boletins.deck.card.benefico') --}}
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
