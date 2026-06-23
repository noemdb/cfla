<div class="form-group form-group-sm input-group pb-1 mb-1">
    <div class="input-group-prepend">
        <div class="input-group-text p-0 m-0 px-1">
            <input class="form-control form-control-sm " size="{{$size ?? ''}}" value="{{$value ?? ''}}" maxlength="{{$maxlength ?? ''}}" name="{{$name ?? ''}}" id="{{$id ?? $name}}" type="text" placeholder="{{$label ?? ''}}" />
        </div>
    </div>
    <div class="form-control pb-1">{{$label ?? ''}}</div>
</div>


