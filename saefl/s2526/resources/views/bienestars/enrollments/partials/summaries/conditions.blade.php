@php
    $conditions = $indicators['conditions'];
    $total_conditions = array_sum($conditions);
    $total = $enrollments->count();
@endphp

<ul class="list-group list-group-flush">
    @foreach ($conditions as $key => $value)
        @if ($value>0)
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
        @endif
    @endforeach

    <li class="list-group-item py-1 border-0 list-group-item-success">
        <div class="d-flex justify-content-between">
            <span class=" font-weight-bold">C.Especiales/Total</span>
            <span class="text-muted">{{$total_conditions}} / {{$total}}</span>
        </div>
    </li>

    <li class="list-group-item py-1 border-0">
        <button class="btn btn-link btn-block" type="button" data-toggle="collapse" data-target="#collapseConditionsOther" aria-expanded="true" aria-controls="collapseConditionsOther">
            @php $total = $conditions_others->count('count_conditions_other'); @endphp
            <div class="d-flex justify-content-between">
                <span class="font-weight-bold">Otras condiciones..</span>
                <span class="text-muted">{{$total}}</span>
            </div>
        </button>
        <div class="collapse" id="collapseConditionsOther">
            <div class="card card-body">
                <div>
                    <ul class="list-group list-group-flush">
                        @foreach ($conditions_others as $item)
                            @if (!empty($item->conditions_other))
                                <li class="list-group-item small">
                                    <div class="d-flex justify-content-between">
                                        <span class="text-uppercase"><span class="">{{$item->conditions_other}}</span></span>
                                        <span class="text-muted">{{$item->count_conditions_other}}</span>
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
