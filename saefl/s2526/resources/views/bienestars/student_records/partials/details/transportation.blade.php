@php
    $transport = $indicators['transport'];
    $total = $transport['total'];
    // $total = array_sum($transport);
@endphp
<ul class="list-group list-group-flush">
    <li class="list-group-item py-1 border-0">
        @php
            $parcial = $transport['private_vehicle'];
            $index = ($total) ? $parcial / $total : null; $index = round($index,2);
            $porcentage = 100 * $index;
        @endphp
        <div class="d-flex justify-content-between">
            <span class=" font-weight-bold">Vehìculo particular</span>
            <span class="text-muted">{{$parcial}}</span>
        </div>
        <div class="progress mb-1" style="height: 1.5rem;">
            <div class="progress-bar progress-bar-striped mb-2 pb-1" role="progressbar" style="width: {{$porcentage}}%;height: 1.5rem;"
                aria-valuenow="{{$porcentage}}" aria-valuemin="0" aria-valuemax="100">
                {{$porcentage}}%
            </div>
        </div>
    </li>
    <li class="list-group-item py-1 border-0">
        @php
            $parcial = $transport['public_vehicle'];
            $index = ($total) ? $parcial / $total : null; $index = round($index,2);
            $porcentage = 100 * $index;
        @endphp
        <div class="d-flex justify-content-between">
            <span class="font-weight-bold">Trasnporte público</span>
            <span class="text-muted">{{$parcial}}</span>
        </div>
        <div class="progress mb-1" style="height: 1.5rem;">
            <div class="progress-bar progress-bar-striped bg-success mb-2 pb-1" role="progressbar" style="width: {{$porcentage}}%;height: 1.5rem;"
                aria-valuenow="{{$porcentage}}" aria-valuemin="0" aria-valuemax="100">
                {{$porcentage}}%
            </div>
        </div>
    </li>
    <li class="list-group-item py-1 border-0">
        @php
            $parcial = $transport['walking'];
            $index = ($total) ? $parcial / $total : null; $index = round($index,2);
            $porcentage = 100 * $index;
        @endphp
        <div class="d-flex justify-content-between">
            <span class=" font-weight-bold">Caminando</span>
            <span class="text-muted">{{$parcial}}</span>
        </div>
        <div class="progress mb-1" style="height: 1.5rem;">
            <div class="progress-bar progress-bar-striped bg-danger mb-2 pb-1" role="progressbar" style="width: {{$porcentage}}%;height: 1.5rem;"
                aria-valuenow="{{$porcentage}}" aria-valuemin="0" aria-valuemax="100">
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
