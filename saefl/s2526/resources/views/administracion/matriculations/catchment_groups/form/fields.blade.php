<div class="form-group">
    @php $name = 'grado_id' @endphp
    <label for="{{ $name }}" class=" font-weight-bold m-0">{{ $list_comment[$name] }}</label>
    {!! Form::select($name, $list_grado, old($name), [
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
    @php $name = 'max' @endphp
    <label for="{{ $name }}" class=" font-weight-bold m-0">{{ $list_comment[$name] }}</label>
    {!! Form::number($name, old($name), [
        'class' => 'form-control',
        'placeholder' => $list_comment[$name],
        'id' => $name,
        'required',
    ]) !!}
</div>

<div class="form-group">
    @php $name = 'min' @endphp
    <label for="{{ $name }}" class=" font-weight-bold m-0">{{ $list_comment[$name] }}</label>
    {!! Form::number($name, old($name), [
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
