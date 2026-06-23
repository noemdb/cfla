<div class="form-group pb-1">
    <label for="{{ $name ?? '' }}" class="m-0">{{ $label ?? '' }}</label>
    {{-- <input class="form-control" value="{{$value ?? ''}}" maxlength="{{$maxlength ?? ''}}" name="{{$name ?? ''}}" id="{{$id ?? $name}}" type="text" placeholder="{{$label ?? ''}}" {{$required ?? ''}}  /> --}}
    @empty($id)
        @php
            $id = $name;
        @endphp
    @endisset
    @empty($required)
        @php
            $required = '';
        @endphp
    @endisset
    {{-- {!! Form::text($name, old($name), ['class' => 'form-control','placeholder'=>$label,'id'=>$id,$required]) !!} --}}
    {!! Form::text($name, old($name), ['class' => 'form-control', 'placeholder' => $label, 'id' => $id, $required]) !!}
</div>
