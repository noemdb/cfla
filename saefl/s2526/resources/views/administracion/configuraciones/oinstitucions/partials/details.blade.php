<div class="d-block">
    <label for="code" class="font-weight-bold text-secondary m-0">{{$list_comment['code'] ?? ''}}</label>
    <div class="alert alert-secondary">
        {{$oinstitucion->code ?? ''}}
    </div>
    <label for="name" class="font-weight-bold text-secondary m-0">{{$list_comment['name'] ?? ''}}</label>
    <div class="alert alert-secondary">
        {{$oinstitucion->name ?? ''}}
    </div>
    <label for="description" class="font-weight-bold text-secondary m-0">{{$list_comment['description'] ?? ''}}</label>
    <div class="alert alert-secondary">
        {{$oinstitucion->description ?? ''}}
    </div>
    <label for="locations" class="font-weight-bold text-secondary m-0">{{$list_comment['locations'] ?? ''}}</label>
    <div class="alert alert-secondary">
        {{$oinstitucion->locations ?? ''}}
    </div>
    <label for="state" class="font-weight-bold text-secondary m-0">{{$list_comment['state'] ?? ''}}</label>
    <div class="alert alert-secondary">
        {{$oinstitucion->state ?? ''}}
    </div>
</div>