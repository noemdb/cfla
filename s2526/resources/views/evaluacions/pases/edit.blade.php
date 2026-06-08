@extends('evaluacions.layouts.dashboard.app')

@section('main')

    <main role="main">

        <div class="card m-1 p-0">

            <div class="alert alert-secondary mb-1 pb-1">
                <div class="btn-group float-right pt-0 pb-2">
                    @include('evaluacions.pases.menus.index')
                </div>
                <h3>Editar Pase Escolar</h3>
            </div>

            <div class="card-body m-1 p-1">

                @include('administracion.elements.messeges.oper_ok') 

                {!! Form::model($pase,['route' => ['evaluacions.permissions.pases.update', $pase->id], 'method' => 'PUT', 'id'=>'form-update_'.$pase->id, 'role'=>'form']) !!}

                    @include('evaluacions.pases.form.fields')

                    <button type="submit" class="btn-pase-edit btn btn-primary btn-block" value="Actualizar" data-id="edit" id="btn-edit-pase-{{$pase->id ?? ''}}">
                        <i class="far fa-save"></i>
                        Actualizar
                    </button>

                {!! Form::close() !!}

            </div>

        </div>

    </main>


@endsection