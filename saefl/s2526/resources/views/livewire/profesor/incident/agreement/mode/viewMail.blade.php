<div>

    <div class="p-1 m-1 border rounded shadow">

        <h5 class="alert-primary py-3 px-2 text-dark font-weight-bolder rounded">
            Vista previa de la notificación</b>
            <button type="button" class="close" wire:click='close()'> <span aria-hidden="true">×</span> </button>
        </h5>

        <div class=" p-2 m-2">

            @include('email.incidents.agreements')
            {{-- @include('email.bienestars.close') --}}

        </div>

    </div>

</div>
