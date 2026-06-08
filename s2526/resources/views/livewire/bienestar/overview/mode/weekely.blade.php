<div>

    <div class="p-1 m-1 border rounded shadow">

        <h5 class="alert alert-success py-1 px-2 text-dark font-weight-bolder rounded">
            <button type="button" class="close" wire:click='close()'> <span aria-hidden="true">×</span> </button>
            <div class="pt-1">
                <b>Reporte de incidencias semanal del docente:</b>
                <div class="d-flex justify-content-between small">
                    <div>{{ $profesor->fullname }}</div>
                </div>
            </div>
        </h5>

        <div class=" p-2 m-2">

            @include('livewire.bienestar.overview.mode.partials.weekely')

        </div>

    </div>

</div>
