{{ Form::hidden('pescolar_id', $autoridad->pescolar_id) }}
{{ Form::hidden('institucion_id', $autoridad->institucion_id) }}

<div class="form-row pt-2">
    <div class="col">
        <label for="pescolar_name" class="m-0 font-weight-bold text-secondary">{{ $list_comment['pescolar_id'] }}</label>
        <div class="alert alert-secondary p-2">
            {{ $autoridad->pescolar->name ?? '' }}
        </div>
    </div>
    <div class="col">
        <label for="institucion_id"
            class="m-0 font-weight-bold text-secondary">{{ $list_comment['institucion_id'] }}</label>
        <div class="alert alert-secondary p-2">
            {{ $autoridad->institucion->name ?? '' }}
        </div>
    </div>
</div>

<div class="form-row pt-2">
    <div class="col">
        <label for="tipo_id" class="m-0 font-weight-bold text-secondary">{{ $list_comment['tipo_id'] }}</label>
        {!! Form::select('tipo_id', $tipo_list, old('tipo_id'), [
            'class' => 'form-control',
            'id' => 'tipo_id' . $autoridad->id,
            'placeholder' => 'Seleccione',
            'required',
        ]) !!}
    </div>
    <div class="col">
        <label for="position" class="m-0 font-weight-bold text-secondary">{{ $list_comment['position'] }}</label>
        {!! Form::text('position', $autoridad->position, [
            'class' => 'form-control',
            'placeholder' => $list_comment['position'],
            'id' => 'position' . $autoridad->id,
            'required',
        ]) !!}
    </div>
</div>

<div class="form-row pt-2">
    <div class="col">
        <label for="name" class="m-0 font-weight-bold text-secondary">{{ $list_comment['name'] }}</label>
        {!! Form::text('name', $autoridad->name, [
            'class' => 'form-control',
            'placeholder' => $list_comment['name'],
            'id' => 'name' . $autoridad->id,
            'required',
        ]) !!}
    </div>
    <div class="col">
        <label for="lastname" class="m-0 font-weight-bold text-secondary">{{ $list_comment['lastname'] }}</label>
        {!! Form::text('lastname', $autoridad->lastname, [
            'class' => 'form-control',
            'placeholder' => $list_comment['lastname'],
            'id' => 'lastname' . $autoridad->id,
            'required',
        ]) !!}
    </div>
</div>

<div class="form-row pt-2">
    <div class="col">
        <label for="ci" class="m-0 font-weight-bold text-secondary">{{ $list_comment['ci'] }}</label>
        {!! Form::text('ci', $autoridad->ci, [
            'class' => 'form-control',
            'placeholder' => $list_comment['ci'],
            'id' => 'ci' . $autoridad->id,
            'required',
        ]) !!}
    </div>
    <div class="col">
        <label for="profile_professional"
            class="m-0 font-weight-bold text-secondary">{{ $list_comment['profile_professional'] }}</label>
        {!! Form::text('profile_professional', $autoridad->profile_professional, [
            'class' => 'form-control',
            'placeholder' => $list_comment['profile_professional'],
            'id' => 'profile_professional' . $autoridad->id,
            'required',
        ]) !!}
    </div>
</div>

<div class="row small">
    <div class="col">
        <div class="form-group">
            <label for="city_birth"
                class="m-0 font-weight-bold text-secondary">{{ $list_comment['city_birth'] ?? '' }}</label>
            {!! Form::text('city_birth', old('city_birth'), [
                'class' => 'form-control',
                'placeholder' => $list_comment['city_birth'],
                'id' => 'city_birth',
                '',
            ]) !!}
        </div>
    </div>
    <div class="col">
        <div class="form-group">
            <label for="town_hall_birth" class="m-0 font-weight-bold text-secondary">Municipio de nacimiento</label>
            {!! Form::text('town_hall_birth', old('town_hall_birth'), [
                'class' => 'form-control',
                'placeholder' => 'Municipio de nacimiento',
                'id' => 'city_birth',
                '',
            ]) !!}
        </div>
    </div>
</div>


<div class="row small">
    <div class="col">
        <div class="form-group">
            <label for="state_birth" class="m-0 font-weight-bold text-secondary">Estado de nacimiento</label>
            {!! Form::text('state_birth', old('state_birth'), [
                'class' => 'form-control',
                'placeholder' => 'Estado de nacimiento',
                'id' => 'state_birth',
                '',
            ]) !!}
        </div>
    </div>
    <div class="col">
        <div class="form-group">
            <label for="country_birth" class="m-0 font-weight-bold text-secondary">País de nacimiento</label>
            {!! Form::select('country_birth', ['VENEZUELA' => 'VENEZUELA', 'OTRO' => 'OTRO'], old('country_birth'), [
                'class' => 'form-control',
                'id' => 'country_birth',
                'placeholder' => 'Seleccione',
                'required',
            ]) !!}
            {{-- {!! Form::text('country_birth', old('country_birth'), ['class' => 'form-control','placeholder'=>'País de nacimiento','id'=>'country_birth','']); !!} --}}
        </div>
    </div>
</div>

<div class="row pb-2">
    <div class="col">
        <label for="user_id" class="m-0 font-weight-bold text-secondary">{{ $list_comment['user_id'] }}</label>
        {!! Form::select('user_id', $user_list, old('user_id'), [
            'class' => 'form-control',
            'id' => 'user_id' . $autoridad->id,
            'placeholder' => 'Seleccione',
        ]) !!}
    </div>
</div>

<div class="row pb-2">
    <div class="col">
        {!! Form::text('mail_cc_address', old('mail_cc_address'), [
            'class' => 'form-control',
            'id' => 'mail_cc_address',
            'placeholder' => 'mail_cc_address',
        ]) !!}
        <label for="mail_cc_address">Dirección de Correo CC ECA</label>
    </div>
</div>
