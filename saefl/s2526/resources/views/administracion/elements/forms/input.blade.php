@php
    $class_input = empty($class_input) ? 'form-control' : $class_input;
@endphp

<div class="form-group pb-1 mb-1">
    <label for="{{ $name ?? '' }}" class="font-weight-bold text-secondary m-0">{{ $label ?? '' }}</label>
    @php $id = (!empty($id)) ? $id  : null; @endphp
    @php $required = (!empty($required)) ? 'required' : null; @endphp
    @php $readonly = (!empty($readonly)) ? 'readonly' : null; @endphp
    {!! Form::text($name, old($name), [
        'class' => $class_input,
        'placeholder' => $label,
        'id' => $id,
        $required,
        $readonly,
    ]) !!}
</div>
