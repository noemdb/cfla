@extends('evaluacions.layouts.dashboard.app')

@section('main')
    <main role="main">

        <div class="card m-1 p-0">

            <div class="alert alert-secondary mb-1 pb-1">
                <div class="btn-group float-right pt-0 pb-2">
                    @include('evaluacions.pases.menus.index')
                </div>
                <h3>Registrar Pase Escolar</h3>
            </div>

            <div class="card-body m-1 p-1">

                @include('administracion.elements.messeges.oper_ok')

                {!! Form::open([
                    'route' => 'evaluacions.permissions.pases.store',
                    'method' => 'POST',
                    'id' => 'form-pases-create',
                    'class' => 'form-signin',
                ]) !!}

                @include('evaluacions.pases.form.fields')

                {!! Form::submit('Registrar', [
                    'class' => 'btn-grupo_estable-create btn btn-primary btn-block',
                    'placeholder' => 'Seleccione',
                    'id' => 'create',
                ]) !!}

                {!! Form::close() !!}

            </div>

        </div>

    </main>
@endsection
