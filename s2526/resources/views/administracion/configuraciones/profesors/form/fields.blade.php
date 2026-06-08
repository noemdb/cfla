@php $readonly = (Request::is('*edit*')) ? 'readonly':null ; @endphp
<div class="row small">
    <div class="col">
        <div class="form-group">
            <label for="ti_teacher"
                class="m-0 font-weight-bold text-secondary">{{ $list_comment['ti_teacher'] ?? '' }}</label>
            <div class="input-group mb-3">
                {!! Form::select('ti_teacher', ['Titular' => 'Titular', 'Suplente' => 'Suplente'], old('ti_teacher'), [
                    'class' => 'form-control',
                    'placeholder' => 'Seleccione',
                    'required',
                ]) !!}
                <div class="input-group-append">
                    <button title="Crear tipo" type="button" class="btn btn-ligth">
                        <i class="fa fa-plus" aria-hidden="true"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>
    <div class="col">
        <div class="form-group">
            <label for="ci_profesor" class="m-0 font-weight-bold text-secondary">CI</label>
            <div class="input-group">
                {!! Form::text('ci_profesor', old('ci_profesor'), [
                    'class' => 'form-control',
                    $readonly,
                    'placeholder' => 'Número de identificación',
                    'id' => 'ci_profesor',
                    'required',
                ]) !!}
                @if (Request::is('*create*'))
                    <div class="input-group-append">
                        <span class="input-group-text" id="result-ci_profesor"></span>
                        <a title="Verificar disponibilidad de número de cédula" id="btn-verificar" class="btn btn-info"
                            href="#">
                            <i class="fa fa-check" aria-hidden="true"></i>
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<div class="row small">
    <div class="col">
        <div class="form-group">
            <label for="name" class="m-0 font-weight-bold text-secondary">Nombres</label>
            {!! Form::text('name', old('name'), [
                'class' => 'form-control',
                'placeholder' => '1er y 2do Nombre',
                'id' => 'name',
                'required',
            ]) !!}
        </div>
    </div>
    <div class="col">
        <div class="form-group">
            <label for="lastname" class="m-0 font-weight-bold text-secondary">Apellidos</label>
            {!! Form::text('lastname', old('lastname'), [
                'class' => 'form-control',
                'placeholder' => '1er y 2do Apellido',
                'id' => 'lastname',
                'required',
            ]) !!}
        </div>
    </div>
</div>

<div class="row small">
    <div class="col">
        <div class="form-group">
            <label for="gender" class="m-0 font-weight-bold text-secondary">Género</label>
            {!! Form::select('gender', ['Masculino' => 'Masculino', 'Femenino' => 'Femenino'], old('gender'), [
                'class' => 'form-control',
                'id' => 'gender',
                'placeholder' => 'Seleccione',
                'required' => 'required',
            ]) !!}
        </div>
    </div>
    <div class="col">
        <div class="form-group pb-1">
            <label for="date_birth"
                class="m-0 font-weight-bold text-secondary">{{ $list_comment['date_birth'] ?? '' }}</label>
            {!! Form::date('date_birth', old('date_birth'), [
                'class' => 'form-control',
                'placeholder' => 'Fecha de la transacción',
                'id' => 'date_birth',
                'required' => 'required',
            ]) !!}
        </div>
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
        </div>
    </div>
</div>

<div class="row small">
    <div class="col">
        <div class="form-group">
            <label for="dir_address" class="m-0 font-weight-bold text-secondary">Dirección</label>
            {!! Form::text('dir_address', old('dir_address'), [
                'class' => 'form-control',
                'placeholder' => 'Dirección',
                'id' => 'dir_address',
                '',
            ]) !!}
        </div>
    </div>
    <div class="col">
        <div class="form-group">
            <label for="phone" class="m-0 font-weight-bold text-secondary">Número de Teléfono <small
                    class=" text-muted small">(Residencial y/o movíl)</small></label>
            {!! Form::text('phone', old('phone'), [
                'class' => 'form-control',
                'placeholder' => 'Número de Teléfono (Residencial y/o movíl)',
                'id' => 'phone',
                '',
            ]) !!}
        </div>
    </div>
</div>

<div class="row small">
    <div class="col">
        <div class="form-group">
            <label for="email" class="m-0 font-weight-bold text-secondary">Correo electrónico</label>
            {!! Form::text('email', old('email'), [
                'class' => 'form-control',
                'placeholder' => 'Correo electrónico',
                'id' => 'email',
                '',
            ]) !!}
        </div>
    </div>
    <div class="col">
        <div class="form-group pb-1">
            <label for="status_active"
                class="m-0 font-weight-bold text-secondary">{{ $list_comment['status_active'] ?? '' }}</label>
            {!! Form::select('status_active', ['true' => 'SI', 'false' => 'NO'], old('status_active'), [
                'class' => 'form-control',
                'id' => 'status_active',
                'placeholder' => 'Seleccione',
            ]) !!}
        </div>
    </div>
</div>

<div class="row small">
    <div class="col">
        <div class="form-group">
            <label for="gsemail"
                class="m-0 font-weight-bold text-secondary">{{ $list_comment['gsemail'] ?? '' }}</label>
            {{-- {!! Form::text('gsemail', old('gsemail'), ['class' => 'form-control',$readonly,'placeholder'=>$list_comment['gsemail'],'id'=>'gsemail','']); !!} --}}
            {!! Form::text('gsemail', old('gsemail'), [
                'class' => 'form-control',
                'placeholder' => $list_comment['gsemail'],
                'id' => 'gsemail',
                '',
            ]) !!}
        </div>
    </div>
    <div class="col">
        <div class="form-group">
            <label for="gspassword"
                class="m-0 font-weight-bold text-secondary">{{ $list_comment['gspassword'] ?? '' }}</label>
            {!! Form::text('gspassword', old('gspassword'), [
                'class' => 'form-control',
                $readonly,
                'placeholder' => $list_comment['gspassword'],
                'id' => 'gspassword',
                '',
            ]) !!}
        </div>
    </div>
</div>

@admin
    @if (Request::is('*edit*'))
        <div class="row pb-2">
            <div class="col">
                <label for="user_id" class="m-0 font-weight-bold text-secondary">{{ $list_comment['user_id'] }}</label>
                {!! Form::select('user_id', $user_list, old('user_id'), [
                    'class' => 'form-control',
                    'placeholder' => 'Seleccione',
                ]) !!}
            </div>
        </div>
    @endif
@endadmin

<div class="row pb-2">
    <div class="col">
        <label for="status_census_taker"
            class="m-0 font-weight-bold text-secondary">{{ $list_comment['status_census_taker'] ?? '' }}</label>
        {!! Form::select('status_census_taker', [true => 'SI', false => 'NO'], old('status_census_taker'), [
            'class' => 'form-control',
            'id' => 'status_census_taker',
            'placeholder' => 'Seleccione',
        ]) !!}
    </div>
</div>

@section('scripts')
    @parent
    <script type="text/javascript">
        $(document).ready(function() {
            $('#btn-verificar').click(function(e) {
                e.preventDefault();
                $('#result-ci_profesor').html('<i class="fas fa-spinner"></i>').fadeOut(500);
                var ci_profesor = $('#ci_profesor').val();
                var dataString = 'ci_profesor=' + ci_profesor;
                $.ajax({
                    type: "GET",
                    url: "{{ route('administracion.ajax.validate.exist.ci_profesor') }}",
                    data: dataString,
                    success: function(data) {
                        $("#result-ci_profesor").removeClass('d-none');
                        $("#result-ci_profesor").addClass('d-block');
                        $('#result-ci_profesor').fadeIn(500).html(data);
                    }
                });
            });
        });
    </script>
@endsection
