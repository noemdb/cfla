<div class="p-1 border rounded h-100">
    <ul class="nav nav-tabs nav-fill">
        <li class="nav-item">
          <a class="nav-link nav-item font-weight-bold active nav-link" href="#mainIdicators" data-toggle="tab">Indicadores Principales</a>
        </li>
        <li class="nav-item">
          <a class="nav-link nav-item font-weight-bold" href="#profesorsIndicators" data-toggle="tab">Profesores</a>
        </li>
        <li class="nav-item">
          <a class="nav-link nav-item font-weight-bold" href="#inscriptionsEstudiant" data-toggle="tab">Estadísticas mensuales</a>
        </li>
        <li class="nav-item">
          <a class="nav-link nav-item font-weight-bold" href="#pestaña4" data-toggle="tab">Inscripciones Estudiantiles</a>
        </li>
        <li class="nav-item">
          <a class="nav-link nav-item font-weight-bold" href="#pestaña5" data-toggle="tab">Distribucción estudiantíl</a>
        </li>
        <li class="nav-item">
          <a class="nav-link nav-item font-weight-bold" href="#pevaluacionProfesor" data-toggle="tab">Planes de Evaluación</a>
        </li>
    </ul>
      
    <div class="tab-content">
        <div class="tab-pane fade show active" id="mainIdicators">
            <div class="p-2">
                @includeIf('academicos.home.partials.labels.estudiantil')
            </div>
        </div>
        <div class="tab-pane fade" id="profesorsIndicators">
            <div class="p-2">
                @include('administracion.boletins.indicators.seguimiento')
            </div>
        </div>
        <div class="tab-pane fade" id="inscriptionsEstudiant">
            <div class="p-2">
                @php $arrMonths = arrMonths(); @endphp
                @isset($arrMonths)
                    <h6 class="alert-secondary p-2 font-weight-bold">Estadísticas mensuales [Nivel - Género - Edad - Cantidad]</h6>
                    <div class="px-2">
                        @include('academicos.tables.controls.estudiants.static')
                    </div>
                @endisset
            </div>
        </div>
        <div class="tab-pane fade" id="pestaña4">
            <div class="row p-1">
                <div class="col-xl-6">
                    @include('academicos.charts.controls.estudiants.estudiants_municipios')
                </div>
                <div class="col-xl-6">
                    @include('academicos.charts.controls.estudiants.estudiants_municipios_pestudio')
                </div>
            </div>
        </div>
        <div class="tab-pane fade" id="pestaña5">
            <div class="p-2">
                <div class="row p-1">
                    <div class="col-md-12 col-lg-6">
                        @include('academicos.charts.controls.inscripciones.inscritoxgenero')
                    </div>
                    <div class="col-md-12 col-lg-6">
                        @include('academicos.charts.controls.inscripciones.genderxplan')
                    </div>
                </div>
            </div>
        </div>
        <div class="tab-pane fade" id="pevaluacionProfesor">
            <div class="p-2">
                @include('academicos.charts.controls.evaluacions.actividades')
            </div>
        </div>
    </div>
</div>


@section('stylesheet')
    @parent
    <link rel="stylesheet" href="{{ asset('vendor/datatables/1.10.20/datatables/css/dataTables.bootstrap4.css') }}">
@endsection

@section('scripts')
    @parent
    <script src="{{ asset("vendor/datatables/1.10.20/datatables/js/jquery.dataTables.js") }}"></script>
    <script src="{{ asset("vendor/datatables/1.10.20/datatables/js/dataTables.bootstrap4.js") }}"></script>
@endsection
