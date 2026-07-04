<div class="d-block">
    <label for="grado_id" class="font-weight-bold text-secondary m-0">{{$list_comment['grado_id'] ?? ''}}</label>
    <div class="alert alert-secondary">
        {{$edescriptiva->grado->pestudio->name ?? ''}}
    </div>
    <label for="grado_id" class="font-weight-bold text-secondary m-0">{{$list_comment['grado_id'] ?? ''}}</label>
    <div class="alert alert-secondary">
        {{$edescriptiva->grado->name ?? ''}}
    </div>
    <label for="name" class="font-weight-bold text-secondary m-0">{{$list_comment['name'] ?? ''}}</label>
    <div class="alert alert-secondary">
        {{$edescriptiva->name ?? ''}}
    </div>
    <label for="description" class="font-weight-bold text-secondary m-0">{{$list_comment['description'] ?? ''}}</label>
    <div class="alert alert-secondary">
        {{$edescriptiva->description ?? ''}}
    </div>
    <label for="amount_student" class="font-weight-bold text-secondary m-0">{{$list_comment['amount_student'] ?? ''}}</label>
    <div class="alert alert-secondary">
        {{$edescriptiva->amount_student ?? ''}}
    </div>
    <label for="observation" class="font-weight-bold text-secondary m-0">{{$list_comment['observation'] ?? ''}}</label>
    <div class="alert alert-secondary">
        {{$edescriptiva->observation ?? ''}}
    </div>
    <label for="status_active" class="font-weight-bold text-secondary m-0">{{$list_comment['status_active'] ?? ''}}</label>
    <div class="alert alert-secondary">
        {{($edescriptiva->status_active=='true') ? 'Activo':'Desactivo'}}
    </div>
</div>
