@extends('representants.layouts.dashboard.app')

@section('main')
    <main role="main">

        <div class="card card-primary mt-2">
            <div class="card-header pb-0 mb-0 alert-secondary">
                {{-- INI Menu rapido --}}
                <div class="btn-group float-right pt-0 pb-2">
                    @include('representants.prepagos.menus.create')
                </div>
                {{-- FIN Menu rapido --}}
                <h4>Registrar una nueva <span class="font-weight-bolder">Notificación de Pago</span></h4>
            </div>

            <div class="card-body">

                @include('representants.elements.forms.errors')

                @include('representants.elements.messeges.oper_ok')

                <div class="card bd-callout bd-callout-primary">

                    <h5 class="card-header">
                        <i class="{{ $icon_menus['nuevo'] }} fa-1x"></i>
                        Datos
                    </h5>

                    <div class="card-body m-1 p-1">

                        <div class="container m-1 p-1">

                            <div class="row">

                                <div class="col-8">
                                    {!! Form::open([
                                        'route' => 'representants.prepagos.store',
                                        'method' => 'POST',
                                        'id' => 'form-prepago-create',
                                        'class' => 'form-signin',
                                    ]) !!}

                                    {{ Form::hidden('representant_id', $representant->id) }}

                                    @include('representants.prepagos.form.fields')

                                    {!! Form::submit('Registrar', ['class' => 'btn-create btn btn-primary btn-block', 'id' => 'create']) !!}

                                    {!! Form::close() !!}
                                </div>

                                <div class="col-4">

                                    @include('representants.prepagos.partials.list')

                                </div>

                            </div>

                        </div>

                    </div>
                </div>

            </div>

        </div>
    </main>
@endsection
