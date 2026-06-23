@php
    $checked = ($value=='true') ? 'checked':'';
    $disabled = (!empty($disabled)) ? 'disabled':'';
    $readonly = (!empty($readonly)) ? 'readonly' : null;
    $modalBtn = (!empty($modalBtn)) ? $modalBtn : null;
    $modalUrl = (!empty($modalUrl)) ? $modalUrl : null;
@endphp
{{-- <fieldset id="{{'checkId'.$id ?? 'inputGroupID'}}"> --}}
    <div class="input-group">
        <div class="input-group-prepend">
            <div class="input-group-text alert-{{ ( !empty( $disabled ) ) ? 'secondary':'dark'}}" data-name="{{ $id ?? $name }}">

                <input class="crt_checkboxes" name="crt_checkboxes[]" type="checkbox" value="{{ $value ?? '' }}" {{ $checked ?? '' }} {{$disabled ?? ''}} {{$readonly ?? ''}}/>

                @if (empty($value)) @php $value = 'false' @endphp @endif

                @if (empty($id))
                    {{ Form::hidden($name, $value,array('class'=>$name)) }}
                @else
                    {{ Form::hidden($name, $value,array('class'=>$name, 'id'=>$id)) }}
                @endif

                @isset($name_ammount) {{ Form::hidden($name_ammount, $value_ammount,array('class'=>$name, 'id'=>'value_ammount_id_'.$value_ammount)) }} @endisset

            </div>
        </div>

        @isset($label) <div class="form-control text-wrap"> {{$label}} @isset($badge) <span class="badge badge-{{ $badgeClass ?? 'light' }} float-right">{{$badge}}</span> @endisset</div> @endisset

        @if($modalBtn) <div class="input-group-append"> @include($modalUrl) </div> @endif

    </div>
{{-- </fieldset> --}}
