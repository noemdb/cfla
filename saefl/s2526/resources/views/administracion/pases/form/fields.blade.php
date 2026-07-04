{{-- user_id
estudiant_id
profesor_id
pensum_id
type
motive
description
destination
date
time
duration
status
code_verification
require_auhtorize_guardian
require_auhtorize_teacher
status_emergency
 --}}

<div class="container-fluid">

    <div class="row">
        <div class="col-sm-12">
            <div class="form-group">
                <label for="estudiant_id" class="font-weight-bold m-0">Estudiante</label>
                <div class="input-group">
                    <div class="input-group-append" style="z-index: 0;">
                        {!! Form::text('help_estudiant', old('help_estudiant'), [
                            'class' => 'form-control small',
                            'placeholder' => 'CI o nombre',
                            'id' => 'help_estudiant',
                        ]) !!}
                    </div>
                    {!! Form::select('estudiant_id', $list_estudiants, old('estudiant_id'), [
                        'class' => 'form-control',
                        'id' => 'estudiant_id',
                        'placeholder' => 'Seleccione',
                        'required',
                    ]) !!}
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-6">
            <div class="form-group">
                @php $name = 'profesor_id' @endphp
                <label for="{{ $name }}" class=" font-weight-bold m-0">{{ $list_comment[$name] }}</label>
                {!! Form::select($name, $list_profesor, old($name), [
                    'id' => $name,
                    'class' => 'form-control',
                    'placeholder' => 'Seleccione',
                    'required',
                ]) !!}
            </div>
        </div>
        <div class="col-sm-6">
            <div class="form-group">
                @php $name = 'pensum_id' @endphp
                <label for="{{ $name }}" class=" font-weight-bold m-0">{{ $list_comment[$name] }}</label>
                {!! Form::select($name, $list_pensum, old($name), [
                    'id' => $name,
                    'class' => 'form-control',
                    'placeholder' => 'Seleccione',
                    'required',
                ]) !!}
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-6">
            <div class="form-group">
                @php $name = 'type' @endphp
                <label for="{{ $name }}" class=" font-weight-bold m-0">{{ $list_comment[$name] }}</label>
                {!! Form::select($name, $list_type, old($name), [
                    'id' => $name,
                    'class' => 'form-control',
                    'placeholder' => 'Seleccione',
                    'required',
                ]) !!}
            </div>
        </div>
        <div class="col-sm-6">
            <div class="form-group">
                @php $name = 'motive' @endphp
                <label for="{{ $name }}" class=" font-weight-bold m-0">{{ $list_comment[$name] }}</label>
                {!! Form::select($name, $list_motive, old($name), [
                    'id' => $name,
                    'class' => 'form-control',
                    'placeholder' => 'Seleccione',
                    'required',
                ]) !!}
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-6">
            <div class="form-group">
                @php $name = 'description' @endphp
                <label for="{{ $name }}" class=" font-weight-bold m-0">{{ $list_comment[$name] }}</label>
                {!! Form::textarea($name, old($name), [
                    'class' => 'form-control',
                    'placeholder' => $list_comment[$name],
                    'id' => $name,
                    'rows' => '1',
                ]) !!}
            </div>
        </div>
        <div class="col-sm-6">
            <div class="form-group">
                @php $name = 'destination' @endphp
                <label for="{{ $name }}" class=" font-weight-bold m-0">{{ $list_comment[$name] }}</label>
                {!! Form::text($name, old($name), [
                    'class' => 'form-control',
                    'placeholder' => $list_comment[$name],
                    'id' => $name,
                ]) !!}
            </div>

        </div>
    </div>

    <div class="row">
        <div class="col-sm-6">
            <div class="form-group">
                @php $name = 'date' @endphp
                <label for="{{ $name }}" class=" font-weight-bold m-0">{{ $list_comment[$name] }}</label>
                {!! Form::date($name, old($name), [
                    'class' => 'form-control',
                    'placeholder' => $list_comment[$name],
                    'id' => $name,
                    'required',
                ]) !!}
            </div>
        </div>
        <div class="col-sm-6">
            <div class="form-group">
                @php $name = 'time' @endphp
                <label for="{{ $name }}" class=" font-weight-bold m-0">{{ $list_comment[$name] }}</label>
                {!! Form::time($name, old($name), [
                    'class' => 'form-control',
                    'placeholder' => $list_comment[$name],
                    'id' => $name,
                    'required',
                ]) !!}
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-4">
            <div class="form-group">
                @php $name = 'require_auhtorize_guardian' @endphp
                <label for="{{ $name }}" class=" font-weight-bold m-0">{{ $list_comment[$name] }}</label>
                {!! Form::select($name, [true => 'SI', false => 'NO'], old($name), [
                    'id' => $name,
                    'class' => 'form-control',
                    'placeholder' => 'Seleccione',
                    'required',
                ]) !!}
            </div>
        </div>
        <div class="col-sm-4">
            <div class="form-group">
                @php $name = 'require_auhtorize_teacher' @endphp
                <label for="{{ $name }}" class=" font-weight-bold m-0">{{ $list_comment[$name] }}</label>
                {!! Form::select($name, [true => 'SI', false => 'NO'], old($name), [
                    'id' => $name,
                    'class' => 'form-control',
                    'placeholder' => 'Seleccione',
                    'required',
                ]) !!}
            </div>
        </div>
        <div class="col-sm-4">
            <div class="form-group">
                @php $name = 'require_auhtorize_manager' @endphp
                <label for="{{ $name }}" class=" font-weight-bold m-0">{{ $list_comment[$name] }}</label>
                {!! Form::select($name, [true => 'SI', false => 'NO'], old($name), [
                    'id' => $name,
                    'class' => 'form-control',
                    'placeholder' => 'Seleccione',
                    'required',
                ]) !!}
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-6">
            <div class="form-group">
                @php $name = 'status' @endphp
                <label for="{{ $name }}" class=" font-weight-bold m-0">{{ $list_comment[$name] }}</label>
                {!! Form::select($name, $list_status, old($name), [
                    'id' => $name,
                    'class' => 'form-control',
                    'placeholder' => 'Seleccione',
                    'required',
                ]) !!}
            </div>
        </div>
        <div class="col-sm-6">
            <div class="form-group">
                @php $name = 'status_emergency' @endphp
                <label for="{{ $name }}" class=" font-weight-bold m-0">{{ $list_comment[$name] }}</label>
                {!! Form::select($name, [true => 'SI', false => 'NO'], old($name), [
                    'id' => $name,
                    'class' => 'form-control',
                    'placeholder' => 'Seleccione',
                    'required',
                ]) !!}
            </div>
        </div>
    </div>

</div>



{{-- <div class="form-group">
    @php $name = 'group_id' @endphp
    <label for="{{ $name }}" class=" font-weight-bold m-0">{{ $list_comment[$name] }}</label>
    {!! Form::select($name, $list_group, old($name), [
        'id' => $name,
        'class' => 'form-control',
        'placeholder' => 'Seleccione',
        'required',
    ]) !!}
</div>

<div class="form-group">
    @php $name = 'name' @endphp
    <label for="{{ $name }}" class=" font-weight-bold m-0">{{ $list_comment[$name] }}</label>
    {!! Form::text($name, old($name), [
        'class' => 'form-control',
        'placeholder' => $list_comment[$name],
        'id' => $name,
        'required',
    ]) !!}
</div>

<div class="form-group">
    @php $name = 'description' @endphp
    <label for="{{ $name }}" class=" font-weight-bold m-0">{{ $list_comment[$name] }}</label>
    {!! Form::text($name, old($name), [
        'class' => 'form-control',
        'placeholder' => $list_comment[$name],
        'id' => $name,
    ]) !!}
</div>

<div class="form-group">
    @php $name = 'date' @endphp
    <label for="{{ $name }}" class=" font-weight-bold m-0">{{ $list_comment[$name] }}</label>
    {!! Form::date($name, old($name), [
        'class' => 'form-control',
        'placeholder' => $list_comment[$name],
        'id' => $name,
        'required',
    ]) !!}
</div>

<div class="form-group">
    @php $name = 'time' @endphp
    <label for="{{ $name }}" class=" font-weight-bold m-0">{{ $list_comment[$name] }}</label>
    {!! Form::time($name, old($name), [
        'class' => 'form-control',
        'placeholder' => $list_comment[$name],
        'id' => $name,
        'required',
    ]) !!}
</div>

<div class="form-group">
    @php $name = 'status_active' @endphp
    <label for="{{ $name }}" class=" font-weight-bold m-0">{{ $list_comment[$name] }}</label>
    {!! Form::select($name, [true => 'Activo', false => 'Desactivo'], old($name), [
        'id' => $name,
        'class' => 'form-control',
        'placeholder' => 'Seleccione',
        'required',
    ]) !!}
</div> --}}

@section('scripts')
    @parent
    <script>
        $(function() {
            $('#estudiant_id').filterByText($('#help_estudiant'), true);
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
    </script>
@endsection
