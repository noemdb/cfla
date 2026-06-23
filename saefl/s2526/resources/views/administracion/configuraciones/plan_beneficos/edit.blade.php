@extends('administracion.layouts.dashboard.app')

@section('main')

    <main role="main" class="col-md-10 ml-sm-auto col-lg-10">

        <div class="card mt-2">
            <div class="card-header alert-warning">
                <div class="btn-group float-right">
                    @include('administracion.configuraciones.plan_beneficos.menus.edit')
                </div>
                <h4>
                    <i class="{{ $icon_menus['editar'] }} fa-1x"></i>
                    Actualizar del Plan Benéfico
                </h4>
            </div>

            <div class="card-body">

                @include('administracion.elements.forms.errors')

                @include('administracion.elements.messeges.oper_ok')

                <div class="card bd-callout bd-callout-primary">

                    <p class="card-header p-2">
                        <span class=" font-size-md">Datos</span>
                        @php $estudiant = $plan_benefico->estudiant; @endphp
                        @include('administracion.configuraciones.descuentos.show.modal.create')
                    </p>

                    <div class="card-body">

                        <div class="row">
                            <div class="col-3">
                                @php $estudiant = $plan_benefico->estudiant @endphp
                                @include('administracion.configuraciones.plan_beneficos.deck.card.benefico')
                            </div>
                            <div class="col-9">
                                {{-- @php $disabled = (empty($estudiant->administrativa)) ? 'disabled="disabled"':'fallo'; @endphp --}}
                                {{-- <fieldset {{$disabled ?? ''}}> --}}
                                    {!! Form::model($plan_benefico,['route' => ['administracion.configuraciones.plan_beneficos.update', $plan_benefico->id], 'method' => 'PUT', 'id'=>'form-update-plan_benefico_'.$plan_benefico->id, 'role'=>'form']) !!}
                                        {{ Form::hidden('pescolar_id', Session::get('pescolar_id')) }}
                                        {{ Form::hidden('estudiant_id', $plan_benefico->estudiant_id) }}
                                        @include('administracion.configuraciones.plan_beneficos.form.fields')
                                        <button type="submit" class="btn-plan_benefico-create btn btn-primary btn-block " value="Actualizar" data-id="create" id="btn-create-plan_benefico-{{$plan_benefico->id ?? ''}}">
                                            <i class="far fa-save"></i>
                                            Actualizar
                                        </button>
                                    {{-- {!! Form::close() !!} --}}
                                </fieldset>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </main>

@endsection

@section('scripts') @parent <script type="text/javascript"> document.title = 'SAEFL - Plan Benéfico, Actualizar'; </script> @endsection
