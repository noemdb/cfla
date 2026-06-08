@extends('administracion.layouts.dashboard.app')

@section('title')
    Autorespondedor - Mensajería Instantanea
@endsection

@section('main')
    <main role="main" class="col-md-10 ml-sm-auto col-lg-10">

        <div class="card card-primary mt-2">


            <div class="card-header pb-0 mb-0 alert-secondary">
                {{-- INI Menu rapido --}}
                <div class="btn-group float-right pt-0 pb-2"> @include('administracion.autoresponders.boptions.menus.edit') </div>
                {{-- FIN Menu rapido --}}

                <h3>Crear <span class="font-weight-bolder">Autorespondedor</span></h3>
            </div>

            <div class="card-body">

                @include('administracion.elements.forms.errors')
                @include('administracion.elements.messeges.oper_ok')

                <div class="container-fluid">

                    <div class="row">

                        <div class="col-12">
                            {!! Form::open([
                                'route' => 'administracion.autoresponders.boptions.store',
                                'method' => 'POST',
                                'id' => 'form-inscripcion-create',
                                'class' => 'form-signin',
                            ]) !!}
                            @include('administracion.autoresponders.boptions.form.fields')
                            {!! Form::submit('Registrar', ['class' => 'btn-create btn btn-primary btn-block', 'id' => 'create']) !!}
                            {!! Form::close() !!}
                        </div>

                    </div>

                </div>

            </div>
        </div>
    </main>
@endsection
