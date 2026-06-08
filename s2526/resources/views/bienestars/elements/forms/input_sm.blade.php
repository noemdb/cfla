<div class="form-group input-group pb-1">
    <div class="input-group-prepend">
        <div class="input-group-text">
            <input class="form-control" size="{{$size ?? ''}}" value="{{$value ?? ''}}" maxlength="{{$maxlength ?? ''}}" name="{{$name ?? ''}}" id="{{$id ?? $name}}" type="text" placeholder="{{$label ?? ''}}" />
        </div>
    </div>
    <div class="form-control">{{$label ?? ''}}</div>
</div>


