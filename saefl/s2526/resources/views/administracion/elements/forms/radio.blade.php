@php
    $name = !empty($name) ? $name : '';
    $check = !empty($check) ? true : false;
    $value = !empty($value) ? $value : '';
    $id = !empty($id) ? $id : '';
    $disabled = !empty($disabled) ? $disabled : null;
    $label = !empty($label) ? $label : null;
    $badge = !empty($badge) ? $badge : null;
    // $name = (!empty($name)) ?  $name: '';
@endphp

<div class="input-group">
    <div class="input-group-prepend">
        <div class="input-group-text">
            {!! Form::radio($name, $value, $check, ['class' => $name, 'id' => $id]) !!}
        </div>
    </div>
    @isset($label)
        <div class="form-control">
            {{ $label }}
            @isset($badge)
                <span class="badge badge-light float-right">{{ $badge }}</span>
            @endisset
        </div>
    @endisset
</div>


{{-- <div class="form-group input-group pb-1 mb-1">
    <div class="input-group-prepend">
        <div class="input-group-text alert-{{ ( !empty( $disabled ) ) ? 'secondary':'dark'}}" data-name="{{ $id ?? $name }}">
            @php
                $checked = ($value=='true') ? 'checked':'';
                $disabled = (!empty($disabled)) ? 'disabled':'';
            @endphp

            @if (empty($value)) @php $value = 'false' @endphp @endif

            @if (empty($id))
                {!! Form::radio($name, $value , false); !!}
            @else
                {!! Form::radio($name, $value , false,['class'=>$name, 'id'=>$id]); !!}
            @endif

            @isset($name_ammount)
                {{ Form::hidden($name_ammount, $value_ammount,array('class'=>$name)) }}
            @endisset
        </div>
    </div>
    @isset($label)
    <div class="form-control">
        {{$label}}
        @isset($badge)
            <span class="badge badge-light float-right">{{$badge}}</span>
        @endisset
    </div>
    @endisset
</div> --}}
