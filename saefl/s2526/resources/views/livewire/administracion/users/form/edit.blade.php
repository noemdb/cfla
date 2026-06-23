<div>

    <div class="p-1 m-1 border rounded shadow">

        <h5 class="alert-warning py-3 px-2 text-dark font-weight-bolder rounded">
            Editar <b>Usuario</b>
            <button type="button" class="close" wire:click='close()'>
                <span aria-hidden="true">×</span>
            </button>
        </h5>

        <div class=" p-2 m-2">

            <div class="text-right font-weight-bold text-sm">
                <div>{{$user->username ?? null}}</div>
                <div>{{$user->fullname ?? null}}</div>
                <div>{{ ($user->profile) ? 'CI: '.$user->profile->card_number : null}}</div>
                {{-- <div class=" text-sm text-muted">{{$user->ci_estudiant}}</div> --}}
                {{-- <div class="{{$user->inscripcion->seccion->grado->class_text_color ?? 'default'}}"> --}}
                    {{-- {{$user->inscripcion->seccion->grado->name ?? ''}} {{$user->inscripcion->seccion->name ?? ''}} --}}
                </div>
            </div>

            <hr>

            @include('livewire.administracion.users.form.fields')

            <div class="btn-group btn-block btn-group-sm" role="group" aria-label="Basic example">
                {!! Form::button('Guardar',['class' => 'form-control btn pt-1 mt-1 btn-primary','wire:click'=>"save()"]) !!}
            </div>

        </div>

    </div>

</div>
