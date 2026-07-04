<div>

    @if ($modeMain)
        @include('livewire.poll.partials.main')
    @endif

    @if ($modeVote)

        <div class="card">
            {{-- <h5 class="card-header"> --}}
                <div class="alert alert-secondary alert-dismissible fade show border-0 py-2 my-0 shadow-sm" role="alert">
                    {{-- <strong>Proceso de Consulta</strong> --}}
                    <div class="fw-bold">{{$poll_main->name}}</div>
                    <button type="button" wire:click="close()" class="btn-close p-2 fw-bold" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            {{-- </h5> --}}
            <div class="card-body py-1 px-1 text-start">
                <livewire:general.poll.index-component :token="$token"/>
            </div>
        </div>

    @endif

</div>

