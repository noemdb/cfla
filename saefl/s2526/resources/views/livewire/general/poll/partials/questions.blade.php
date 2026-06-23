<div class="mb-2 fw-bold h2 text-center text-muted">Proceso de Consulta</div>

<div class="card pb-3 mb-3 p-2 shadow-sm bg-light"
    style="{{ $poll_main->image_url ? 'background-image: url(' . asset($poll_main->image_url) . '); background-repeat: repeat-y; background-position: center top;' : null }}">

    <div class="card-body px-1">
        <div class="text-end">
            <small wire:loading.delay.shortest class="text-muted small px-2">
                Procesando...
            </small>
        </div>
        <div class="h5 pb-0 text-left ">
            <span class="fw-normal small">Nombre: </span>
            <div class=" text-muted fw-bold">{{ $poll_main->name ?? null }}</div>
        </div>
        <hr class="p-1 m-1">
        <div class="fw-normal ps-2">
            <div class=" pl-3 text-secondary pb-0 small">Participante: {{ $poll_token->email_hide ?? null }}</div>
            <div class=" pl-3 text-secondary pb-0 small">Descripción: {{ $poll_main->description ?? null }}</div>
            {{-- <div class=" pl-3 text-secondary pb-0 small">Comienzo: {{ $poll_main->start->format('d-m-Y H:i:s A') ?? null }}</div> --}}
            <div class=" pl-3 text-secondary pb-0 small">Finalización: {{ $poll_main->end->format('d-m-Y H:i:s A') ?? null }}</div>

            <livewire:general.poll.component.answers-componet :token="$token" />

        </div>

        <hr class="">

        <div class="pl-1" id="selector-questions">

            <div>Preguntas:</div>

            <livewire:general.poll.component.questions-componet :token="$token" />

            <livewire:general.poll.component.options-componet :token="$token"/>

        </div>

    </div>

</div>
