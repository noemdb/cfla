@php $readonly = (Request::is('*edit*')) ? 'readonly':null ; @endphp
<label for="representant_id" class="m-0">Representante</label>
<div class="input-group pb-3">
    <div class="input-group-append" style="z-index: 0;">
        {!! Form::text('help_representante', old('help_representante'), [
            'class' => 'form-control',
            'placeholder' => 'CI del representante',
            'id' => 'help_representante',
        ]) !!}
    </div>
    {!! Form::select('representant_id', $list_representant, old('representant_id'), [
        'class' => 'form-control',
        'id' => 'representant_id',
        'required' => 'required',
        'placeholder' => 'Seleccione',
    ]) !!}
    <div class="input-group-append">
        <a title="Nuevo Representante" class="btn btn-dark" href="{{ route('administracion.representants.create') }}">
            <i class="fa fa-plus" aria-hidden="true"></i>
        </a>
    </div>
</div>

<label for="ci_estudiant" class="m-0">Tipo y número de identificación</label>
<div class="input-group">

    <div class="input-group-append" style="z-index: 0;">
        {!! Form::select('type_ci_id', $list_type_ci, old('type_ci_id'), [
            'class' => 'form-control',
            'id' => 'type_ci_id',
            'required' => 'required',
            'placeholder' => 'Seleccione',
        ]) !!}
    </div>

    {!! Form::text('ci_estudiant', old('ci_estudiant'), [
        'class' => 'form-control',
        'placeholder' => 'Número de identificación',
        'id' => 'ci_estudiant',
        'required',
    ]) !!}

    <div class="input-group-append">
        <span class="input-group-text" id="result-ci_estudiant"></span>

        @if (Request::is('*edit*'))
            <a title="Nuevo Estudiante" class="btn btn-dark" href="{{ route('administracion.estudiants.create') }}">
                <i class="fa fa-plus" aria-hidden="true"></i>
            </a>
        @endif

        @if (Request::is('*create*'))
            <a title="Verificar disponibilidad de número de cédula" id="btn-verificar" class="btn btn-info"
                href="#">
                <i class="fa fa-check" aria-hidden="true"></i>
            </a>
            <button title="Editar datos de estudiantes" id="btn-edit-est" type="button" class="btn btn-warning btn-sm">
                <i class="{{ $icon_menus['editar'] ?? '' }} fa-1x"></i>
            </button>
        @endif
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
    {{-- {!! Form::text('country_birth', old('country_birth'), ['class' => 'form-control','placeholder'=>'País de nacimiento','id'=>'country_birth','']); !!} --}}
    {!! Form::select('country_birth', $list_country_birth, old('country_birth'), [
        'class' => 'form-control',
        'id' => 'country_birth',
        'placeholder' => 'Seleccione',
        'required',
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
    <label for="obs_resumen_final"
        class="font-weight-bold text-secondary m-0">{{ $list_comment['obs_resumen_final'] ?? '' }}</label>
    {!! Form::textarea('obs_resumen_final', old('obs_resumen_final'), [
        'class' => 'form-control',
        'placeholder' => $list_comment['obs_resumen_final'],
        'id' => 'obs_resumen_final',
        'rows' => '4',
    ]) !!}
</div>

{{-- <div class="form-group">
    <label for="phone" class="m-0">
        Números de Teléfono
        <span class="font-weight-bold text-secondary pl-2">[Ingresa los números separados por un / ]</span>
    </label>
    {!! Form::text('phone', old('phone'), ['class' => 'form-control','placeholder'=>'Número de Teléfono (Residencial y/o movíl)','id'=>'phone','']); !!}
</div> --}}
{{-- <div class="form-group">
    <label for="email" class="m-0">Correo electrónico</label>
    {!! Form::text('email', old('email'), ['class' => 'form-control','placeholder'=>'Correo electrónico','id'=>'email','']); !!}
</div> --}}

<div class="form-group">
    <label for="gsemail" class="m-0">Correo electrónico Clases Virtuales</label>
    {!! Form::text('gsemail', old('gsemail'), [
        'class' => 'form-control',
        'placeholder' => 'Correo electrónico',
        'id' => 'gsemail',
        '',
    ]) !!}
</div>

<div class="form-group">
    <label for="status_active" class="m-0">{{ $list_comment['status_active'] ?? null }}</label>
    {!! Form::select('status_active', ['true' => 'Activo', 'false' => 'Desactivo'], old('status_active'), [
        'class' => 'form-control form-control-sm',
        'id' => 'status_active',
        'placeholder' => 'Seleccione',
    ]) !!}
</div>

<div class="form-group">
    <label for="status_notice" class="m-0">{{ $list_comment['status_notice'] ?? null }}</label>
    {!! Form::select('status_notice', [true => 'SI', false => 'NO'], old('status_notice'), [
        'class' => 'form-control form-control-sm',
        'id' => 'status_notice',
        'placeholder' => 'Seleccione',
    ]) !!}
</div>

@admon
    <div class="form-group">
        <label for="status_blacklist" class="m-0">{{ $list_comment['status_blacklist'] ?? null }}</label>
        {!! Form::select('status_blacklist', ['true' => 'SI', 'false' => 'NO'], old('status_blacklist'), [
            'class' => 'form-control form-control-sm',
            'id' => 'status_blacklist',
            'placeholder' => 'Seleccione',
        ]) !!}
    </div>
@endadmon

@control
    <div class="form-group">
        <label for="count_passes" class="m-0">Cantidad de Pases</label>
        {!! Form::number('count_passes', old('count_passes'), [
            'class' => 'form-control',
            'placeholder' => 'Cantidad de Pases',
            'id' => 'count_passes',
            '',
        ]) !!}
    </div>
@endcontrol

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
@section('scripts')
    @parent

    <script type="text/javascript">
        $(function() {
            $('#representant_id').filterByText($('#help_representante'), true);
        });

        jQuery.fn.filterByText = function(textbox, selectSingleMatch) {
            return this.each(function() {
                var select = this;
                var options = [];
                $(select).find('option').each(function() {
                    options.push({
                        value: $(this).val(),
                        text: $(this).text()
                    });
                });
                $(select).data('options', options);
                $(textbox).bind('change keyup', function() {
                    var options = $(select).empty().scrollTop(0).data('options');
                    var search = $.trim($(this).val());
                    var regex = new RegExp(search, 'gi');

                    $.each(options, function(i) {
                        var option = options[i];
                        if (option.text.match(regex) !== null) {
                            $(select).append(
                                $('<option>').text(option.text).val(option.value)
                            );
                        }
                    });
                    if (selectSingleMatch === true &&
                        $(select).children().length === 1) {
                        $(select).children().get(0).selected = true;
                    }
                });
            });
        };

        //verificar existencia de la ci ingresada
        $(document).ready(function() {
            $('#btn-verificar').click(function(e) {
                e.preventDefault();
                $('#result-ci_estudiant').html('<i class="fas fa-spinner"></i>').fadeOut(500);

                var ci_estudiant = $('#ci_estudiant').val();
                var dataString = 'ci_estudiant=' + ci_estudiant;

                $.ajax({
                    type: "GET",
                    url: "{{ route('administracion.ajax.validate.exist.studiant_ci') }}",
                    data: dataString,
                    success: function(data) {
                        $("#result-ci_estudiant").removeClass('d-none');
                        $("#result-ci_estudiant").addClass('d-block');
                        $('#result-ci_estudiant').fadeIn(500).html(data);
                    }
                });
            });
        });
        //btn para ir a editar los datos del EST
        $(document).ready(function() {
            $('#btn-edit-est').click(function(e) {
                e.preventDefault();
                var ci_estudiant = $('#ci_estudiant').val();
                console.log(ci_estudiant);
                var dataString = '?ci_estudiant=' + ci_estudiant;
                console.log(dataString);
                var url = "{{ route('administracion.estudiants.edit') }}" + dataString;
                window.open(url, '_self');
            });
        });
    </script>
@endsection

@section('stylesheet')
    @parent
    <link href="{{ asset('css/floating-labels.css') }}" rel="stylesheet">
    <link href="{{ asset('vendor/bootstrap-datepicker/1.6.4/css/bootstrap-datepicker3.css') }}" rel="stylesheet">
@endsection
