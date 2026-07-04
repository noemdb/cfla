<div class="form">
    <label for="pestudio_id" class="font-weight-bold text-secondary m-0">{{$list_comment['pestudio_id'] ?? ''}}</label>
    <div class="alert alert-secondary">
        {{$grado->pestudio->name ?? ''}}
    </div>
    <label for="name" class="font-weight-bold text-secondary m-0">{{$list_comment['name'] ?? ''}}</label>
    <div class="alert alert-secondary">
        {{$grado->name ?? ''}}
    </div>
    <label for="code" class="font-weight-bold text-secondary m-0">{{$list_comment['code'] ?? ''}}</label>
    <div class="alert alert-secondary">
        {{$grado->code ?? ''}}
    </div>
    <label for="code_sm" class="font-weight-bold text-secondary m-0">{{$list_comment['code_sm'] ?? ''}}</label>
    <div class="alert alert-secondary">
        {{$grado->code_sm ?? ''}}
    </div>
    <label for="description" class="font-weight-bold text-secondary m-0">{{$list_comment['description'] ?? ''}}</label>
    <div class="alert alert-secondary">
        {{$grado->description ?? ''}}
    </div>
    <label for="status_active" class="font-weight-bold text-secondary m-0">{{$list_comment['status_active'] ?? ''}}</label>
    <div class="alert alert-secondary">
        {{($grado->status_active=='true') ? 'Activo':'Desactivo'}}
    </div>    
</div>
