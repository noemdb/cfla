<div>

    @php $estudiants = $pevaluacion->estudiants; @endphp
    <div class="card">

        <div class="alert alert-secondary alert-dismissible fade show border-0 py-2 my-0 shadow-sm" role="alert">
            <div class="fw-bold">{{$evaluacion->description}}</div>
            <button type="button" wire:click="close()" class="btn-close p-2 fw-bold" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>

        <div class="card-body py-1 text-start">
            {{$estudiants}}
        </div>
    </div>

</div>
