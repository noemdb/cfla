<div class="p-1 rounded h-100">
    <ul class="nav nav-tabs nav-fill">
        <li class="nav-item">
          <a class="nav-link nav-item font-weight-bold active nav-link" href="#mainIdicatorsLapsoId{{$lapso->id}}" data-toggle="tab">Indicadores Principales</a>
        </li>
        <li class="nav-item">
          <a class="nav-link nav-item font-weight-bold" href="#profesorsIndicatorsLapsoId{{$lapso->id}}" data-toggle="tab">Profesores</a>
        </li>
    </ul>
      
    <div class="tab-content border border-top-0">
        <div class="tab-pane fade show active" id="mainIdicatorsLapsoId{{$lapso->id}}">
            <div class="p-4">

                @includeIf('leaders.home.partials.labels.estudiantil')

                {{-- @include('leaders.charts.lessonns.diary') --}}

                @include('leaders.charts.controls.evaluacions.actividades')                

            </div>
        </div>
        <div class="tab-pane fade" id="profesorsIndicatorsLapsoId{{$lapso->id}}">
            <div class="p-4">
                @include('leaders.home.indicators.seguimiento')
            </div>
        </div>
    </div>
</div>


