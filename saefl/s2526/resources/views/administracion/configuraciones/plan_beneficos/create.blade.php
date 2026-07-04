@extends('administracion.layouts.dashboard.app')

@section('main')

    <main role="main" class="col-md-10 ml-sm-auto col-lg-10">

        <div class="card mt-2">
            <div class="card-header">
                {{-- INI Menu rapido --}}
                <div class="btn-group float-right">
                    @include('administracion.configuraciones.plan_beneficos.menus.crud')
                </div>
                {{-- FIN Menu rapido --}}
                <h4>
                    <i class="{{ $icon_menus['crear'] ?? ''}} fa-1x"></i>
                    Asignar <span class=" font-weight-bolder">Plan Benéfico</span>
                </h4>
            </div>

            <div class="card-body">

                @include('administracion.elements.forms.errors')

                @include('administracion.elements.messeges.oper_ok')

                <div class="card bd-callout bd-callout-primary">

                    <p class="card-header p-2">
                        <span class=" font-size-md">Datos</span>
                        @include('administracion.configuraciones.descuentos.show.modal.create')
                    </p>

                    <div class="card-body">

                        <div class="row">
                            <div class="col-3">
                                @include('administracion.configuraciones.plan_beneficos.deck.card.benefico')
                            </div>
                            <div class="col-9">
                                @php $disabled = (empty($estudiant->administrativa)) ? true: false; @endphp
                                <fieldset {{($disabled) ? 'disabled="disabled"' : null}}>
                                    {!! Form::open(['route' => 'administracion.configuraciones.plan_beneficos.store', 'method' => 'POST', 'id'=>'form-inscripcion-create', 'class'=>'form-signin']) !!}
                                        {!! Form::hidden('estudiant_id', $estudiant->id) !!}
                                        @include('administracion.configuraciones.plan_beneficos.form.fields')
                                        <button type="submit" class="btn-inscripcion-create btn btn-primary btn-block" value="Registrar" data-id="create" id="btn-create-inscripcion-{{$inscripcion->id ?? ''}}">
                                            <i class="far fa-save"></i>
                                            Registrar
                                        </button>
                                    {!! Form::close() !!}
                                </fieldset>
                                @if ($disabled)
                                    <div class="alert alert-secondary" role="alert">
                                        <strong>No tiene inscripción administrativa</strong>
                                    </div>
                                @endif
                            </div>
                        </div>

                    </div>
                </div>

            </div>
        </div>
    </main>

@endsection

@section('scripts') @parent <script type="text/javascript"> document.title = 'SAEFL - Plan Benéfico, registrar'; </script> @endsection
