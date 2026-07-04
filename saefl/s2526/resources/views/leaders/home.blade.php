@extends('leaders.layouts.dashboard.app')

@section('main')

    <main role="main">

        <div class="container-fluid pt-1 w-100">
            <div class="row">

                <div class="col-sm-12 px-0">
                    <div class="border rounded">

                        <div class="alert alert-primary pb-0 mb-1" role="alert">
                            <strong class="d-block">Jefe de Área de Conocimiento</strong>
                            @foreach ($area_conocimientos as $item)
                                <small class="text-uppercase font-weight-bold text-muted" style="font-size: 1rem">
                                    {{$loop->iteration }}. {{$item->name ?? null}}                                 
                                </small>

                                <div class="px-2 text-muted small">
                                    <span class=" font-weight-bold">ÁREAS DE FORMACIÓN: </span>
                                    @php $pevaluacions = $item->getPevaluacions(); @endphp
                                    @foreach ($pevaluacions as $sitem)
                                        <span class=" font-weight-light text-nowrap">[ {{$sitem->asignatura_name_sm ?? null}} ]</span>
                                        {{-- @if (! $loop->last ) || @endif --}}
                                    @endforeach
                                </div>

                            @endforeach
                        </div>                        

                        <div class="p-2 m-2">

                            <nav>
                                <div class="nav nav-tabs nav-fill" id="nav-tab" role="tablist">
                                    @foreach($lapsos as $lapso)
                                        <a class="nav-item nav-link {{($loop->iteration==$lapso_active->id) ? 'active':''}} font-weight-bold text-uppercase small"
                                            id="nav-header-tab-gprofesors-{{$lapso->id}}" data-toggle="tab"
                                            href="#nav-content-gprofesors-{{$lapso->id}}" role="tab" aria-controls="nav-home" aria-selected="true">
                                            {{$lapso->name ?? ''}}
                                        </a>
                                    @endforeach
                                </div>
                            </nav>
                            
                            <div class="tab-content border border-top-0" id="nav-tabContent">                    
                                @foreach ($lapsos as $lapso)
                                    <div class="tab-pane fade {{($loop->iteration==$lapso_active->id) ? ' show active ':''}}" id="nav-content-gprofesors-{{$lapso->id ?? ''}}" role="tabpanel" aria-labelledby="nav-header-home-tab-{{$lapso->id ?? ''}}">
                                        <div class="d-block p-4">
                                            @includeif('leaders.home.indicators')                         
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                        </div>

                    </div>
                </div>

            </div>
        </div>

    </main>

    {{-- @include('proyectos.home.modals.main') --}}

@endsection

@section('chartjs')
    @parent
    <script src="{{ asset("js/Chart.bundle.js") }}"></script>
    <script src="{{ asset("js/ChartFunction.js") }}"></script>
    <script src="{{ asset("js/ChartEvent.js") }}"></script>
@endsection

