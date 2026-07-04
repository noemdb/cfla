<div class="form-group">
    <label for="{{$name ?? ''}}" class="mb-0 pb-0">{{$label ?? ''}}</label>
<textarea class="form-control" id="{{$id ?? $name}}" name="{{$name ?? ''}}" rows="{{$rows ?? ''}}" placeholder="{{$placeholder ?? $label}}">{{$value ?? ''}}</textarea>
</div>
