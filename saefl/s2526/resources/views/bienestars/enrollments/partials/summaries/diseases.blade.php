@php
    $illness = $indicators['illness'];
    $total_illness = array_sum($illness);
    $total = $enrollments->count();
@endphp

<ul class="list-group list-group-flush">
    @foreach ($illness as $key => $value)
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
            <span class=" font-weight-bold">C.Enfermedades/Total</span>
            <span class="text-muted">{{$total_illness}} / {{$total}}</span>
        </div>
    </li>

    <li class="list-group-item py-1 border-0">
        <button class="btn btn-link btn-block" type="button" data-toggle="collapse" data-target="#collapseIllnessOthers" aria-expanded="true" aria-controls="collapseIllnessOthers">
            @php $total = $illness_others->count('count_illness_other'); @endphp
            <div class="d-flex justify-content-between">
                <span class="font-weight-bold">Otras enfermedades graves</span>
                <span class="text-muted">{{$total}}</span>
            </div>
        </button>
        <div class="collapse" id="collapseIllnessOthers">
            <div class="card card-body">
                <div>
                    <ul class="list-group list-group-flush">
                        @foreach ($illness_others as $item)
                        <li class="list-group-item small">
                            <div class="d-flex justify-content-between">
                                <span class="text-uppercase"><span class="">{{$item->illness_other}}</span></span>
                                <span class="text-muted">{{$item->count_illness_other}}</span>
                            </div>
                        </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </li>



</ul>
