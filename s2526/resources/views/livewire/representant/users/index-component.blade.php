<div>

    @if (Session::has('operp_ok'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
            {!! Session::get('operp_ok') !!}
        </div>
    @endif


    <form wire:submit.prevent="save">
        <div class="text-right font-weight-bold text-sm" wire:click="save()">
            <div>{{$user->username ?? null}}</div>
            <div>{{$user->fullname ?? null}}</div>
            <div>{{ ($user->profile) ? 'CI: '.$user->profile->card_number : null}}</div>
        </div>

        <hr >

        @include('livewire.representant.users.form.fields')

        {!! Form::button('Guardar',['wire:click'=>"save()",'class' => 'form-control btn pt-1 mt-1 btn-primary']) !!}

    </form>

</div>
