<div>

    <div class="p-1 m-1">

        <h6 class="alert alert-success py-1 px-2 text-dark font-weight-bolder rounded">

            <div class="pt-1">
                <b>Calendario de Servicios Ejecutados:</b>

            </div>
        </h6>

        <div class="p-1 m-1">

            <div class="d-flex justify-content-between">
                <div class="text-muted font-weight-bold">Desde {{$start->format('d-m-Y')}} hasta {{$end->format('d-m-Y')}} </div>
            </div>

            @include('livewire.profesor.social-actions.calendar.calendar')

        </div>

    </div>

</div>
