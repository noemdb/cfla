<div>

    <div class="p-1 m-1 border rounded shadow">

        <h5 class="alert-warning py-3 px-2 text-dark font-weight-bolder rounded">
            Editar <b>rol</b>
            <button type="button" class="close" wire:click='closeRolEdit()'>
                <span aria-hidden="true">×</span>
            </button>
        </h5>

        <div class=" p-2 m-2">

            <div class="text-right font-weight-bold text-sm text-muted">
                <span>{{$user->username ?? null}} |</span>
                <span>{{$user->fullname ?? null}} |</span>
                <span>{{ ($user->profile) ? 'CI: '.$user->profile->card_number : null}}</span>
            </div>

            <hr class="p-0 m-0">

            @include('livewire.administracion.users.form.rol.fields')

            <div class="btn-group btn-block btn-group-sm" role="group" aria-label="Basic example">
                {!! Form::button('Guardar',['class' => 'form-control btn pt-1 mt-1 btn-primary','wire:click'=>"saveRol()"]) !!}
            </div>

        </div>

    </div>

</div>
