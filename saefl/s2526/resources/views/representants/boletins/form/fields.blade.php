<label for="ti_teacher" class="m-0">Tipo de facilitador</label>
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
<label for="ci_boletin" class="m-0">CI</label>
<div class="input-group">

    {!! Form::text('ci_boletin', old('ci_boletin'), [
        'class' => 'form-control',
        'placeholder' => 'Número de identificación',
        'id' => 'ci_boletin',
        'required',
    ]) !!}

    <div class="input-group-append">
        <span class="input-group-text" id="result-ci_boletin"></span>
        <a title="Verificar disponibilidad de número de cédula" id="btn-verificar" class="btn btn-info" href="#">
            <i class="fa fa-check" aria-hidden="true"></i>
        </a>
    </div>

</div>

<div class="form-group">
    <label for="name" class="m-0">Nombres</label>
    {!! Form::text('name', old('name'), [
        'class' => 'form-control',
        'placeholder' => '1er y 2do
      Nombre',
        'id' => 'name',
        'required',
    ]) !!}
</div>
<div class="form-group">
    <label for="lastname" class="m-0">Apellidos</label>
    {!! Form::text('lastname', old('lastname'), [
        'class' => 'form-control',
        'placeholder' => '1er y 2do
      Apellido',
        'id' => 'lastname',
        'required',
    ]) !!}
</div>

<div class="form-group">
    <label for="gender" class="m-0">Género</label>
    {!! Form::select('gender', ['Masculino' => 'Masculino', 'Femenino' => 'Femenino'], old('gender'), [
        'class' => 'form-control',
        'id' => 'gender',
        'placeholder' => 'Seleccione',
        'required' => 'required',
    ]) !!}
</div>

<div class="form-group pb-1">
    <label for="date_birth" class="m-0">Fecha de Nacimiento</label>
    {!! Form::text('date_birth', old('date_birth'), [
        'class' => 'form-control datepicker',
        'placeholder' => 'Fecha de Nacimiento',
        'id' => 'date_birth',
        'required',
        'readonly',
        'maxlength' => '10',
    ]) !!}
</div>

<div class="form-group">
    <label for="city_birth" class="m-0">Ciudad de nacimiento</label>
    {!! Form::text('city_birth', old('city_birth'), [
        'class' => 'form-control',
        'placeholder' => 'Ciudad de nacimiento',
        'id' => 'city_birth',
        '',
    ]) !!}
</div>
<div class="form-group">
    <label for="town_hall_birth" class="m-0">Municipio de nacimiento</label>
    {!! Form::text('town_hall_birth', old('town_hall_birth'), [
        'class' => 'form-control',
        'placeholder' => 'Municipio de nacimiento',
        'id' => 'city_birth',
        '',
    ]) !!}
</div>
<div class="form-group">
    <label for="state_birth" class="m-0">Estado de nacimiento</label>
    {!! Form::text('state_birth', old('state_birth'), [
        'class' => 'form-control',
        'placeholder' => 'Estado de nacimiento',
        'id' => 'state_birth',
        '',
    ]) !!}
</div>
<div class="form-group">
    <label for="country_birth" class="m-0">País de nacimiento</label>
    {!! Form::text('country_birth', old('country_birth'), [
        'class' => 'form-control',
        'placeholder' => 'País de nacimiento',
        'id' => 'country_birth',
        '',
    ]) !!}
</div>

<div class="form-group">
    <label for="dir_address" class="m-0">Dirección</label>
    {!! Form::text('dir_address', old('dir_address'), [
        'class' => 'form-control',
        'placeholder' => 'Dirección',
        'id' => 'dir_address',
        '',
    ]) !!}
</div>
<div class="form-group">
    <label for="phone" class="m-0">Número de Teléfono <small class=" text-muted small">(Residencial y/o
            movíl)</small></label>
    {!! Form::text('phone', old('phone'), [
        'class' => 'form-control',
        'placeholder' => 'Número de Teléfono (Residencial y/o movíl)',
        'id' => 'phone',
        '',
    ]) !!}
</div>
<div class="form-group">
    <label for="email" class="m-0">Correo electrónico</label>
    {!! Form::text('email', old('email'), [
        'class' => 'form-control',
        'placeholder' => 'Correo electrónico',
        'id' => 'email',
        '',
    ]) !!}
</div>


@section('scripts')
    @parent

    <script type="text/javascript">
        $(document).ready(function() {
            $('.crt_checkboxes').click(function(e) {
                //alert('123');
                var div = $(this).parents('div'); //console.log(div); //fila contentiva de la data
                var name = div.data('name');
                console.log(name);
                var checked = $(this).prop('checked');
                console.log(checked);
                var input = '[name=' + name + ']';
                $(input).val(checked);
                console.log($(input).val());
            });
        });
        $(document).ready(function() {
            $('#btn-verificar').click(function(e) {
                e.preventDefault();
                $('#result-ci_boletin').html('<i class="fas fa-spinner"></i>').fadeOut(500);

                var ci_boletin = $('#ci_boletin').val();
                var dataString = 'ci_boletin=' + ci_boletin;

                $.ajax({
                    type: "GET",
                    url: "{{ route('administracion.ajax.validate.exist.ci_boletin') }}",
                    data: dataString,
                    success: function(data) {
                        $("#result-ci_boletin").removeClass('d-none');
                        $("#result-ci_boletin").addClass('d-block');
                        $('#result-ci_boletin').fadeIn(500).html(data);
                    }
                });
            });
        });
    </script>
@endsection

@section('stylesheet')
    @parent
    <link href="{{ asset('css/floating-labels.css') }}" rel="stylesheet">
    <link href="{{ asset('vendor/bootstrap-datepicker/1.6.4/css/bootstrap-datepicker3.css') }}" rel="stylesheet">
@endsection

@section('scripts')
    @parent
    <script src="{{ asset('vendor/bootstrap-datepicker/1.6.4/js/bootstrap-datepicker.js') }}"></script>
    <script src="{{ asset('vendor/bootstrap-datepicker/1.6.4/locales/bootstrap-datepicker.es.min.js') }}"></script>
    <script type="text/javascript">
        $('.datepicker').datepicker({
            format: "yyyy-mm-dd",
            language: "es",
            autoclose: true,
            startView: 2
        });
    </script>
@endsection
