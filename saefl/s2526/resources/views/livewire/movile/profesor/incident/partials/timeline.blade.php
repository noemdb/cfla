<div class="panel panel-default shadow px-2 rounded border">
    <div class="panel-heading">
        <h5 class="alert alert-light py-3 px-2 text-dark fw-bolder rounded border-bottom">
            <div class="text-info text-center">
                <i class="{{ $icon_menus['tline'] ?? '' }} fa-1x "></i> Línea de tiempo
            </div>
        </h5>
    </div>
    <!-- /.panel-heading -->
    <div class="panel-body">
        <ul class="timeline">

            @forelse ($events as $date => $items)
                @php
                    $fecha = Jenssegers\Date\Date::createFromFormat('Y-m-d', $date);
                    $inverted = $loop->iteration % 2 == 0 ? true : false;
                @endphp
                <li class="{{ $inverted ? 'timeline-inverted' : null }}">
                    <div class="timeline-badge">
                        <i class="fas fa-circle"></i>
                    </div>

                    <div class="timeline-panel">
                        <div class="timeline-heading">
                            <h6 class="timeline-title fw-bold {{ $inverted ? 'text-left' : 'text-right' }}">
                                <span class=" text-capitalize">{{$fecha->format('D')}}</span> <span class="">{{$fecha->format('j M y')}}</span>

                            </h6>
                        </div>
                        <div class="timeline-body">

                            <ul class="list-group list-group-flush px-1">
                                @php krsort($items); @endphp
                                @foreach ($items as $hour => $item)
                                    @php $incident = $item['incident']; @endphp
                                    @php $date = $item['date']; @endphp
                                    @php $date_hour = ($date) ? $date->format('h:i:s a') : null; @endphp
                                    <li class="list-group-item list-group-item-{{ $item['class'] }} rounded mb-1 shadow-sm p-0">

                                        <div class="fw-bold p-1 text-{{ $item['class'] }} {{ $inverted ? 'text-left' : 'text-right' }}">
                                            {{ $date_hour }}
                                        </div>

                                        <div>
                                            @include('livewire.movile.profesor.incident.partials.icon')
                                        </div>

                                        <div>
                                            [BE-I{{ $incident->id ?? null}}]
                                        </div>
                                        <div>
                                            {{ $item['description'] }}
                                        </div>

                                        {{-- <div class="container">
                                            <div class="row">
                                                <div class="col-4">
                                                    <div class="d-flex justify-content-center align-items-center fw-bold">
                                                        @include('livewire.bienestar.incident.mode.timeline.icon')
                                                    </div>
                                                </div>
                                                <div class="col-8">
                                                    <div class="fw-bold"> <span class="font-weight-light">[BE-I{{ $incident->id ?? null}}]</span> {{ $item['description'] }} </div>
                                                </div>
                                            </div>
                                        </div> --}}

                                    </li>
                                @endforeach
                            </ul>

                        </div>
                    </div>

                </li>

                @empty

                    <li>No hay datos</li>

                @endforelse

            </ul>
        </div>
        <!-- /.panel-body -->
    </div>

    @section('stylesheets')
        @parent
        <link href="{{ asset('css/timeline.css') }}" rel="stylesheet">
    @endsection
