@extends('administracion.layouts.dashboard.app')

@section('main')

    <main role="main" class="col-md-10 ml-sm-auto col-lg-10">

        <div class="card bd-callout bd-callout-{{$grado->color ?? 'default'}} mt-2">
            <div class="card-header alert-secondary">
                <div class="btn-group float-right">
                    {{-- @include('administracion.evaluacions.menus.create') --}}
                </div>
                <h3>
                    <i class="{{$icon_menus['evaluacion'] ?? ''}} text-info" aria-hidden="true"></i>
                    Plan de evaluación
                </h3>
                <span class=" d-block p-0 m-0 small"><span class="small">{{$pevaluacion->description ?? ''}}</span>
                <span class="small">{{$pensum->asignatura->name ?? ''}}</span>
            </div>

            <div class="card-body p-2 m-2">

                <div class="dropdown-divider mb-0"></div>

                @include('administracion.elements.forms.errors')

                @include('administracion.elements.messeges.oper_ok')

                <div class="content">
                    <div class="row">

                        <div class="col-sm-3">
                            <h5 class="card-title">Resumen</h3>
                            <div class="dropdown-divider mb-0"></div>
                            @include('administracion.pevaluacions.partials.resumen')
                        </div>

                        <div class="col-sm-6">

                            <div class="card shadow p-1 mb-5 bg-white rounded">
                                <div class="card-body p-1 m-1">
                                    <h6 class="card-title"> <b>Registro de Evaluaciones</b></h6>
                                    <div class="dropdown-divider mb-0"></div>
                                    {!! Form::open(['route' => 'administracion.evaluacions.store', 'method' => 'POST', 'id'=>'form-evaluacions-create', 'class'=>'form-signin']) !!}
                                    @include('administracion.pevaluacions.evaluacions.form.fields')

                                    <div class="btn-group btn-block" role="group" aria-label="Basic example">
                                        <button type="submit" class="btn-pevaluacions-create btn btn-info" value="Registrar" data-id="create" id="btn-create-pevaluacions">
                                            {{-- <i class="far fa-save"></i>  --}}
                                            <i class="fa fa-arrow-right" aria-hidden="true"></i>
                                            Continuar
                                        </button>
                                        {{-- <button type="submit" class="btn-pevaluacions-create btn btn-primary" value="Registrar" data-id="create" id="btn-create-pevaluacions">
                                            <i class="far fa-save"></i> Finalizar
                                        </button> --}}
                                        <a class="btn btn-primary" href="{{ route('administracion.evaluacions.index') }}" role="button">
                                            <i class="far fa-save"></i> Finalizar
                                        </a>
                                    </div>
                                    {!! Form::close() !!}
                                </div>
                            </div>

                        </div>

                        @if (!empty($pevaluacion->evaluacions->first()))
                            <div class="col-sm-3">
                                <div class="h-100 rounded p-1 m-1 alert-{{ ($pevaluacion->status_eva_complete) ? 'success':'warning' }}">
                                    <h5 class="card-title">Lista de Evaluaciones registradas</h3>
                                    <div class="dropdown-divider mb-0"></div>
                                    @php $evaluacions = (!empty($pevaluacion->evaluacions)) ? $pevaluacion->evaluacions:null; @endphp
                                    @includewhen(($evaluacions),'administracion.pevaluacions.partials.evaluacion')
                                </div>
                            </div>
                        @endif

                    </div>
                </div>

            </div>
        </div>

    </main>

@endsection

@section('scripts')
  @parent
  <script type="text/javascript">
    $(document).ready(function() {
      if ( {{ ($pevaluacion->status_eva_complete) ? 1:0 }} ) {
          $('#form-evaluacions-create').find('input, textarea, button, select').attr('disabled','disabled');
      }
    });
  </script>
@endsection




@section('scripts')
    @parent

@endsection
