<div class="d-block">
    <label for="grado_id" class="font-weight-bold text-secondary m-0">{{$list_comment['grado_id'] ?? ''}}</label>
    <div class="alert alert-secondary">
        {{$seccion->grado->pestudio->name ?? ''}}
    </div>
    <label for="grado_id" class="font-weight-bold text-secondary m-0">{{$list_comment['grado_id'] ?? ''}}</label>
    <div class="alert alert-secondary">
        {{$seccion->grado->name ?? ''}}
    </div>
    <label for="name" class="font-weight-bold text-secondary m-0">{{$list_comment['name'] ?? ''}}</label>
    <div class="alert alert-secondary">
        {{$seccion->name ?? ''}}
    </div>
    <label for="description" class="font-weight-bold text-secondary m-0">{{$list_comment['description'] ?? ''}}</label>
    <div class="alert alert-secondary">
        {{$seccion->description ?? ''}}
    </div>
    <label for="amount_student" class="font-weight-bold text-secondary m-0">{{$list_comment['amount_student'] ?? ''}}</label>
    <div class="alert alert-secondary">
        {{$seccion->amount_student ?? ''}}
    </div>
    <label for="observation" class="font-weight-bold text-secondary m-0">{{$list_comment['observation'] ?? ''}}</label>
    <div class="alert alert-secondary">
        {{$seccion->observation ?? ''}}
    </div>
    <label for="comment_final" class="font-weight-bold text-secondary m-0">{{$list_comment['comment_final'] ?? ''}}</label>
    <div class="alert alert-secondary">
        {{$seccion->comment_final ?? ''}}
    </div>
    <label for="status_active" class="font-weight-bold text-secondary m-0">{{$list_comment['status_active'] ?? ''}}</label>
    <div class="alert alert-secondary">
        {{($seccion->status_active=='true') ? 'Activo':'Desactivo'}}
    </div>
</div>
