<div class="container card bd-callout bd-callout-{{$pestudio->color ?? ''}}">
    <label for="pescolar_id" class="font-weight-bold text-secondary m-0">{{$list_comment['pescolar_id'] ?? ''}}</label>
    <div class="alert alert-secondary">
        {{$peducativo->pescolar->name ?? ''}}
    </div>
    <label for="name" class="font-weight-bold text-secondary m-0">{{$list_comment['name'] ?? ''}}</label>
    <div class="alert alert-secondary">
        {{$peducativo->name ?? ''}}
    </div>
    <label for="description" class="font-weight-bold text-secondary m-0">{{$list_comment['description'] ?? ''}}</label>
    <div class="alert alert-secondary">
        {{$peducativo->description ?? ''}}
    </div>
    <label for="order" class="font-weight-bold text-secondary m-0">{{$list_comment['order'] ?? ''}}</label>
    <div class="alert alert-secondary">
        {{$peducativo->order ?? ''}}
    </div>
    <label for="status_active" class="font-weight-bold text-secondary m-0">{{$list_comment['status_active'] ?? ''}}</label>
    <div class="alert alert-secondary">
        {{($peducativo->status_active=='true') ? 'Activo':'Desactivo'}}
    </div>    
</div>
