<div class=" d-block">
    <label for="code" class="font-weight-bold text-secondary m-0">{{$list_comment['code'] ?? ''}}</label>
    <div class="alert alert-secondary">
        {{$lapso->code ?? ''}}
    </div>
    <label for="code_sm" class="font-weight-bold text-secondary m-0">{{$list_comment['code_sm'] ?? ''}}</label>
    <div class="alert alert-secondary">
        {{$lapso->code_sm ?? ''}}
    </div>
    <label for="name" class="font-weight-bold text-secondary m-0">{{$list_comment['name'] ?? ''}}</label>
    <div class="alert alert-secondary">
        {{$lapso->name ?? ''}}
    </div>
    <label for="finicial" class="m-0 font-weight-bold text-secondary">{{$list_comment['finicial'] ?? ''}}</label>
    <div class="alert alert-secondary pb-1">
        {{ f_date($lapso->finicial) ?? ''}}
    </div>
    <label for="ffinal" class="m-0 font-weight-bold text-secondary">{{$list_comment['ffinal'] ?? ''}}</label>
    <div class="alert alert-secondary pb-1">
        {{ f_date($lapso->ffinal) ?? ''}}
    </div>
</div>
