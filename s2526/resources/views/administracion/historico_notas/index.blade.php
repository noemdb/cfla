@extends('administracion.layouts.dashboard.app')

@section('title') - Histórico de Notas @endsection

@section('main')

    <main role="main" class="col-md-10 ml-sm-auto col-lg-10">

        <div class="card mt-2">
            @php $class_action = (empty($estudiant->historico_nota->id)) ? 'primary':'success'; @endphp
            <div class="card-header alert-{{ (empty($estudiant_id)) ? 'secondary' : $class_action }}">
                <div class="btn-group float-right pt-0 pb-2">
                    @include('administracion.historico_notas.menus.index')
                </div>
                <h4 class="pb-0 mb-0">
                    <i class="{{ $icon_menus['estudiante'] ?? ''}} fa-1x "></i>
                    {{ (empty($estudiant->historico_nota->id)) ? 'Crear':'Editar' }} <span class=" font-weight-bolder">Histórico de Notas</span>
                </h4>

                @if (!empty($estudiant->id))
                    <span class="small text-dark p-0 m-0">
                        <strong>
                        <span id="estudiant_{{ $estudiant->id ?? ''}}">
                                {{$estudiant->name ?? 'fallo'}}
                                ({{$estudiant->ci_estudiant ?? 'fallo'}})
                                <span class="text-uppercase {{ (!empty($estudiant->grado)) ? $estudiant->grado->class_text_color:null}}">
                                    [{{$estudiant->full_inscripcion ?? 'fallo'}}]
                                </span>
                            </span>
                        </strong>
                    </span>
                @endif
            </div>
            <div class="px-2 small">
                @if (!empty($historico_nota->id))
                    @component('administracion.elements.progress.bars_xs')
                        @slot('title','% de Notas Registradas')
                        @slot('actual_ammount',$historico_nota->getRealNotas($pestudio->id))
                        @slot('goal_ammount',$historico_nota->getGoalNotas($pestudio->id))
                        @slot('porcentage','true')
                    @endcomponent
                @endif
            </div>

            <div class="card-body">


                @include('administracion.elements.forms.errors')
                @include('administracion.elements.messeges.oper_ok')

                @include('administracion.historico_notas.form.search',['route'=>'administracion.historico_notas.index'])

                <hr>

                @if (!empty($pestudio->id) && !empty($estudiant->id))

                    @if (empty($historico_nota->id))

                        @include('administracion.historico_notas.form.create')

                    @else

                        @include('administracion.historico_notas.form.edit')

                    @endif

                @endif

            </div>
        </div>
    </main>

@endsection

@section('scripts')
    @parent
    <script src="{{ asset("vendor/bootstrap-datepicker/1.6.4/js/bootstrap-datepicker.js") }}"></script>
    <script src="{{asset('vendor/bootstrap-datepicker/1.6.4/locales/bootstrap-datepicker.es.min.js')}}"></script>
    <script type="text/javascript">
        $('.datepicker').datepicker({
                format: "mm-yyyy",
                language: "es",
                autoclose: true,
                minViewMode: 'months',
                startView: 'months'
            });
    </script>
@endsection
@section('stylesheet')
    @parent
    <link href="{{ asset('vendor/bootstrap-datepicker/1.6.4/css/bootstrap-datepicker3.css') }}" rel="stylesheet">
@endsection
