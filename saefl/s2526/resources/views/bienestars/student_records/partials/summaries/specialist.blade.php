@php
    $treated_by_specialist = $indicators['treated_by_specialist'];
    $total = $treated_by_specialist['total'];
    // $total = array_sum($treated_by_specialist);
@endphp

<ul class="list-group list-group-flush">
    <li class="list-group-item py-1 border-0">
        @php
            $parcial = $treated_by_specialist['treated_by_specialist_true'];
            $index = ($total) ? $parcial / $total : null; $index = round($index,2);
            $porcentage = 100 * $index;
        @endphp
        <div class="d-flex justify-content-between">
            <span class="font-weight-bold">Si</span>
            <span class="text-muted">{{$parcial}}</span>
        </div>
        <div class="progress mb-1" style="height: 1.5rem;">
            <div class="progress-bar progress-bar-striped mb-2 pb-1 bg-warning" role="progressbar" style="width: {{$porcentage}}%;height: 1.5rem;"
                aria-valuenow="25" aria-valuemin="0" aria-valuemax="100">
                {{$porcentage}}%
            </div>
        </div>
    </li>
    <li class="list-group-item py-1 border-0">
        @php
            $parcial = $treated_by_specialist['treated_by_specialist_false'];
            $index = ($total) ? $parcial / $total : null; $index = round($index,2);
            $porcentage = 100 * $index;
        @endphp
        <div class="d-flex justify-content-between">
            <span class="font-weight-bold">No</span>
            <span class="text-muted">{{$parcial}}</span>
        </div>
        <div class="progress mb-1" style="height: 1.5rem;">
            <div class="progress-bar progress-bar-striped bg-info mb-2 pb-1" role="progressbar" style="width: {{$porcentage}}%;height: 1.5rem;" aria-valuenow="10" aria-valuemin="0" aria-valuemax="100">
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
    <li class="list-group-item py-1 border-0">
        <button class="btn btn-link btn-block" type="button" data-toggle="collapse" data-target="#collapseSpecialist" aria-expanded="true" aria-controls="collapseSpecialist">
            @php $total = $treated_specialists->count('count_conditions_other'); @endphp
            <div class="d-flex justify-content-between">
                <span class="font-weight-bold">Especialistas</span>
                <span class="text-muted">{{$total}}</span>
            </div>
        </button>
        <div class="collapse" id="collapseSpecialist">
            <div class="card card-body">
                <div>
                    <ul class="list-group list-group-flush">
                        @foreach ($treated_specialists as $item)
                            @if (!empty($item->specialist))
                                <li class="list-group-item small">
                                    <div class="d-flex justify-content-between">
                                        <span class="text-uppercase"><span class="">{{$item->specialist}}</span></span>
                                        <span class="text-muted">{{$item->count_specialist}}</span>
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


{{--


<ul class="list-group list-group-flush">
  <li class="list-group-item py-1 border-0">
      Si
      <div class="progress mb-1" style="height: 1.5rem;">
        <div class="progress-bar progress-bar-striped mb-2 pb-1" role="progressbar" style="width: 17%;height: 1.5rem;" aria-valuenow="17" aria-valuemin="0" aria-valuemax="100">
          17%
        </div>
      </div>
  </li>
  <li class="list-group-item py-1 border-0">
      No
      <div class="progress mb-1" style="height: 1.5rem;">
        <div class="progress-bar progress-bar-striped bg-success mb-2 pb-1" role="progressbar" style="width: 83%;height: 1.5rem;" aria-valuenow="83" aria-valuemin="0" aria-valuemax="100">
          83%
        </div>
      </div>
  </li>


</ul> --}}
