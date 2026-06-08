@php
    $vaccination = $indicators['vaccination'];
    $total = $vaccination['total'];
    // $total = array_sum($vaccination);
@endphp

<ul class="list-group list-group-flush">
    <li class="list-group-item py-1 border-0">
        @php
            $parcial = $vaccination['vaccination_schedule_true'];
            $index = ($total) ? $parcial / $total : null; $index = round($index,2);
            $porcentage = 100 * $index;
        @endphp
        <div class="d-flex justify-content-between">
            <span class="font-weight-bold">Si</span>
            <span class="text-muted">{{$parcial}}</span>
        </div>
        <div class="progress mb-1" style="height: 1.5rem;">
            <div class="progress-bar progress-bar-striped mb-2 pb-1" role="progressbar" style="width: {{$porcentage}}%;height: 1.5rem;"
                aria-valuenow="25" aria-valuemin="0" aria-valuemax="100">
                {{$porcentage}}%
            </div>
        </div>
    </li>
    <li class="list-group-item py-1 border-0">
        @php
            $parcial = $vaccination['vaccination_schedule_false'];
            $index = ($total) ? $parcial / $total : null; $index = round($index,2);
            $porcentage = 100 * $index;
        @endphp
        <div class="d-flex justify-content-between">
            <span class="font-weight-bold">No</span>
            <span class="text-muted">{{$parcial}}</span>
        </div>
        <div class="progress mb-1" style="height: 1.5rem;">
            <div class="progress-bar progress-bar-striped bg-warning mb-2 pb-1" role="progressbar" style="width: {{$porcentage}}%;height: 1.5rem;" aria-valuenow="10" aria-valuemin="0" aria-valuemax="100">
                {{$porcentage}}%
            </div>
        </div>
    </li>
    <li class="list-group-item py-1 border-0 list-group-item-success">
        <div class="d-flex justify-content-between">
            <span class=" font-weight-bold">Total</span>
            <span class="text-muted">{{$total}}</span>
        </div>
    </li>
</ul>
