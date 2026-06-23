<div>

    <div class="p-1 m-1 border rounded shadow">

        <h5 class="alert-primary py-3 px-2 text-dark font-weight-bolder rounded">
            Vista previa del correo electrónico.
            <button type="button" class="close" wire:click='close()'> <span aria-hidden="true">×</span> </button>
        </h5>

        <div class="p-1 m-1">
            @if ($attendee)
                {{-- @include('email.polls.messege') --}}
                @include('email.polls.notify')
            @else
                <div class="alert alert-danger">
                    No hay un usuario destinatario para este mensaje, revise sus datos.
                </div>
            @endif
        </div>

    </div>

</div>
