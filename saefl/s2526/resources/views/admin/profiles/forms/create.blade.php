<div class="card bd-callout bd-callout-{{ $class_form_create_profile ?? 'form' }}">
    <div class="card-header">
        Formulario para el Registro de Nuevo Perfil.
    </div>
    <div class="card-body">

        {{-- <form> --}}
        {!! Form::open([
            'route' => 'profiles.store',
            'method' => 'POST',
            'id' => 'form-profile-create-' . (isset($user->id) ? $user->id : 'create'),
        ]) !!}

        @if (isset($user->id))
            {{ Form::hidden('user_id', $user->id) }}
        @else
            <div class="form-label-group pb-1">

                {!! Form::select('user_id', $user_list, old('user_id'), ['class' => 'form-control', 'placeholder' => 'Usuario']) !!}
                {{-- <label for="is_active">{{ trans('validation.attributes.is_active') }}</label> --}}

            </div>
        @endif

        {{-- partial con el formulario y campos --}}
        @include('admin.profiles.forms.fields')

        <button type="submit" class="btn-profile-create btn btn-primary btn-block" value="create"
            data-user="{{ $user->id ?? 'create' }}">
            <span class="glyphicon glyphicon-save" aria-hidden="true"></span>
            Registrar
        </button>
        <button type="reset" class="btn-profile-reset btn btn-info btn-block" value="Reset">
            <span class="glyphicon glyphicon-refresh" aria-hidden="true"></span>
            Reset
        </button>

        {!! Form::close() !!}
        {{-- </form> --}}

    </div>
</div>
