@extends('administracion.layouts.dashboard.app')

@section('main')

    <main role="main" class="col-md-10 ml-sm-auto col-lg-10">

        <div class="card bd-callout bd-callout-{{$grado->color ?? 'default'}} mt-2">
            <div class="card-header alert-secondary">
                <div class="btn-group float-right">
                    @include('administracion.pevaluacions.menus.create')
                </div>
                <h5>
                    Plan de evaluación
                    <span class=" d-block p-0 m-0 small"><span class="small">{{$pevaluacion->description ?? ''}}</span>
                    <span class="small">{{$pensum->asignatura->name ?? ''}}</span>
                </h5>
            </div>

            <div class="card-body p-2 m-2">

                <div class="dropdown-divider mb-0"></div>

                @include('administracion.elements.forms.errors')

                @include('administracion.elements.messeges.oper_ok')

                @php $modeCreate = (empty($pevaluacion->id)) ? true : false ; @endphp
                @php $modeLoad = ! $modeCreate ; @endphp

                <div class="content-fluid">

                    <div class="row">

                        @if ($modeCreate)

                            @include('administracion.pevaluacions.partials.create')

                        @endif

                        @if ($modeLoad)

                            @include('administracion.pevaluacions.partials.load')
                            
                        @endif

                    </div>

                    @if ($modeLoad)
                        <hr>

                        <div class=" alert alert-secondary font-weight-bold">Agregar un Componente de Formación</div>

                        <div class="row">

                            <div class="col-sm-3">
                                <h5 class="card-title">Resumen</h5>
                                <div class="dropdown-divider mb-0"></div>
                                @include('administracion.pevaluacions.partials.info')
                            </div>

                            <div class="col-sm-6">
                                @include('administracion.pevaluacions.form.create')
                            </div>

                            <div class="col-sm-3">
                                <div class="alert alert-secondary">Componentes de Formación registrados para: {{$pensum->asignatura->name ?? ''}}</div>
                                <div class="dropdown-divider mb-0"></div>
                                @include('administracion.pevaluacions.partials.training')
                            </div>

                        </div>
                    @endif                   

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
