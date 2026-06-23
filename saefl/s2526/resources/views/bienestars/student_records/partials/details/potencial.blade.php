@php
    $sports_potentials = $indicators['sports_potentials'];
    $sports_potentials_first = array_slice($sports_potentials, 0, 8);
    $sports_potentials_others = array_slice($sports_potentials, 9);
    $total = array_sum($sports_potentials);
@endphp

<ul class="list-group list-group-flush">
    @foreach ($sports_potentials_first as $key => $value)
        <li class="list-group-item py-1 border-0">
            {{-- {{$key}} {{$value}} --}}
            @php
                $parcial = $value;
                $index = ($total) ? $parcial / $total : null; $index = round($index,2);
                $porcentage = 100 * $index;
            @endphp

            <div class="d-flex justify-content-between">
                <span class="font-weight-bold">{{$key}}</span>
                <span class="text-muted">{{$parcial}}</span>
            </div>
            <div class="progress mb-1" style="height: 1.5rem;">
                <div class="progress-bar progress-bar-striped mb-2 pb-1" role="progressbar" style="width: {{$porcentage}}%;height: 1.5rem;"
                    aria-valuenow="25" aria-valuemin="0" aria-valuemax="100">
                    {{$porcentage}}%
                </div>
            </div>
        </li>
    @endforeach

    <li class="list-group-item py-1 border-0 list-group-item-success">
        <div class="d-flex justify-content-between">
            <span class=" font-weight-bold">Total</span>
            <span class="text-muted">{{$total}}</span>
        </div>
    </li>

    <li class="list-group-item py-1 border-0">
        <button class="btn btn-link btn-block" type="button" data-toggle="collapse" data-target="#collapseSportsPotentials" aria-expanded="true" aria-controls="collapseSportsPotentials">
            @php $total = count($sports_potentials_others); @endphp
            <div class="d-flex justify-content-between">
                <span class="font-weight-bold">Otras Potencialidades</span>
                <span class="text-muted">{{$total}}</span>
            </div>
        </button>
        <div class="collapse" id="collapseSportsPotentials">
            <div class="card card-body">
                <div>
                    <ul class="list-group list-group-flush">
                        @foreach ($sports_potentials_others  as $key => $value)
                            @if (!empty($key) && !empty($value))
                                <li class="list-group-item small">
                                    <div class="d-flex justify-content-between">
                                        <span class="text-uppercase"><span class="">{{$key}}</span></span>
                                        <span class="text-muted">{{$value}}</span>
                                    </div>
                                </li>
                            @endif
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </li>

</ul>
