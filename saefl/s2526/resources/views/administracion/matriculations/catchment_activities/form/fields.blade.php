{{-- <fieldset disabled> --}}
<fieldset>
    <div class="form-group">
        @php $name = 'group_id' @endphp
        <label for="{{ $name }}" class=" font-weight-bold m-0">{{ $list_comment[$name] }}</label>
        {!! Form::select($name, $list_group, old($name), [
            'id' => $name,
            'class' => 'form-control',
            'placeholder' => 'Seleccione',
            'required',
        ]) !!}
    </div>
</fieldset>

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
</div>
