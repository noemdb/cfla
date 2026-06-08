<div class="form-group">
    <label for="{{$name ?? ''}}">{{$label ?? ''}}</label>
    <input class="form-control" maxlength="{{$maxlength ?? '50'}}" name="{{$name ?? ''}}" id="{{$id ?? $name}}" type="password" />
</div>
