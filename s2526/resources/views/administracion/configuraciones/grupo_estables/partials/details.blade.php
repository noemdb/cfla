<div class="d-block">
    <label for="code" class="font-weight-bold text-secondary m-0">{{$list_comment['code'] ?? ''}}</label>
    <div class="alert alert-secondary">
        {{$grupo_estable->code ?? ''}}
    </div>
    <label for="code_sm" class="font-weight-bold text-secondary m-0">{{$list_comment['code_sm'] ?? ''}}</label>
    <div class="alert alert-secondary">
        {{$grupo_estable->code_sm ?? ''}}
    </div>
    <label for="name" class="font-weight-bold text-secondary m-0">{{$list_comment['name'] ?? ''}}</label>
    <div class="alert alert-secondary">
        {{$grupo_estable->name ?? ''}}
    </div>
    <label for="description" class="font-weight-bold text-secondary m-0">{{$list_comment['description'] ?? ''}}</label>
    <div class="alert alert-secondary">
        {{$grupo_estable->description ?? ''}}
    </div>
    <label for="hour_t_week" class="font-weight-bold text-secondary m-0">{{$list_comment['hour_t_week'] ?? ''}}</label>
    <div class="alert alert-secondary">
        {{$grupo_estable->hour_t_week ?? ''}}
    </div>
    <label for="hour_p_week" class="font-weight-bold text-secondary m-0">{{$list_comment['hour_p_week'] ?? ''}}</label>
    <div class="alert alert-secondary">
        {{$grupo_estable->hour_p_week ?? ''}}
    </div>
    <label for="observations" class="font-weight-bold text-secondary m-0">{{$list_comment['observations'] ?? ''}}</label>
    <div class="alert alert-secondary">
        {{$grupo_estable->observations ?? ''}}
    </div>
    <label for="status_belongs_ins" class="font-weight-bold text-secondary m-0">{{$list_comment['status_belongs_ins'] ?? ''}}</label>
    <div class="alert alert-secondary">
        {{($grupo_estable->status_belongs_ins=='true') ? 'SI':'NO'}}
    </div>

</div>

