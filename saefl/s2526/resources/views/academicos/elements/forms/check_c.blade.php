<div class="form-group input-group pb-1 mb-1">
    <div class="input-group-prepend">
        <div class="input-group-text alert-{{ ( empty( $disabled ) ) ? 'light':'dark'}}" data-name="{{ $id ?? $name }}">
            {{ Form::checkbox($name, $value,array('class'=>'crt_checkboxes')) }}
        </div>        
    </div>
    @isset($label)    
        <div class="form-control">
            {{$label}}
        </div> 
    @endisset  
</div>


