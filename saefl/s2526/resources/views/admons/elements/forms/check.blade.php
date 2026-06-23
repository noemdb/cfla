<div class="form-group input-group pb-1 mb-1">
    <div class="input-group-prepend">
        <div class="input-group-text alert-{{ ( !empty( $disabled ) ) ? 'secondary':'dark'}}" data-name="{{ $id ?? $name }}">            
            @php
                $checked = ($value=='true') ? 'checked':'';
                $disabled = (!empty($disabled)) ? 'disabled':'';
            @endphp
            <input class="crt_checkboxes" name="crt_checkboxes[]" type="checkbox" value="{{ $value ?? '' }}" {{ $checked ?? '' }} {{$disabled ?? ''}}/>

            @if (empty($value)) @php $value = 'false' @endphp @endif
            
            @if (empty($id))
                {{ Form::hidden($name, $value,array('class'=>$name)) }}                
            @else
                {{ Form::hidden($name, $value,array('class'=>$name, 'id'=>$id)) }}                
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
</div>


