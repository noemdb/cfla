<div class="p-1 rounded h-100">
    <ul class="nav nav-tabs nav-fill">
        <li class="nav-item">
          <a class="nav-link nav-item font-weight-bold active nav-link" href="#mainIdicators" data-toggle="tab">Indicadores Principales</a>
        </li>
        <li class="nav-item">
          <a class="nav-link nav-item font-weight-bold" href="#profesorsIndicators" data-toggle="tab">Profesores</a>
        </li>
        <li class="nav-item">
          <a class="nav-link nav-item font-weight-bold" href="#pevaluacionProfesor" data-toggle="tab">Planes de Evaluación</a>
        </li>
        </li>
        <li class="nav-item">
          <a class="nav-link nav-item font-weight-bold" href="#leadersProfesor" data-toggle="tab">Lecciones</a>
        </li>
    </ul>
      
    <div class="tab-content">
        <div class="tab-pane fade show active" id="mainIdicators">
            <div class="p-2">
                @includeIf('evaluacions.home.partials.labels.estudiantil')
            </div>
        </div>
        <div class="tab-pane fade" id="profesorsIndicators">
            <div class="p-4 m-4">
                @include('evaluacions.home.indicators.seguimiento')                
            </div>
        </div>
        <div class="tab-pane fade" id="pevaluacionProfesor">
            <div class="p-2">
                @include('evaluacions.home.charts.evaluacions.actividades')
            </div>
        </div>
        <div class="tab-pane fade" id="leadersProfesor">
            <div class="p-2">
                @include('evaluacions.home.charts.leaders.lessons')
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
