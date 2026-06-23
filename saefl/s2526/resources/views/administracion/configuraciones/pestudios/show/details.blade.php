<div class="container card bd-callout bd-callout-{{$pestudio->color ?? ''}}">
    <label for="peducativo_id" class="font-weight-bold text-secondary m-0">{{$list_comment['peducativo_id'] ?? ''}}</label>
    <div class="alert alert-secondary">
        {{$pestudio->peducativo->name ?? ''}}
    </div>
    <label for="code" class="font-weight-bold text-secondary m-0">{{$list_comment['code'] ?? ''}}</label>
    <div class="alert alert-secondary">
        {{$pestudio->code ?? ''}}
    </div>
    <label for="name" class="font-weight-bold text-secondary m-0">{{$list_comment['name'] ?? ''}}</label>
    <div class="alert alert-secondary">
        {{$pestudio->name ?? ''}}
    </div>
    <label for="order" class="font-weight-bold text-secondary m-0">{{$list_comment['order'] ?? ''}}</label>
    <div class="alert alert-secondary">
    {{$pestudio->order ?? ''}}
    </div>
    <label for="description" class="font-weight-bold text-secondary m-0">{{$list_comment['description'] ?? ''}}</label>
    <div class="alert alert-secondary">
        {{$pestudio->description ?? ''}}
    </div>
    <label for="description_aux" class="font-weight-bold text-secondary m-0">{{$list_comment['description_aux'] ?? ''}}</label>
    <div class="alert alert-secondary">
        {{$pestudio->description_aux ?? ''}}
    </div>
    <label for="status_build_promotion" class="font-weight-bold text-secondary m-0">{{$list_comment['status_build_promotion'] ?? ''}}</label>
    <div class="alert alert-secondary">
        {{ ($pestudio->status_build_promotion=='true') ? 'SI':'NO'}}
    </div>
    <label for="title" class="font-weight-bold text-secondary m-0">{{$list_comment['title'] ?? ''}}</label>
    <div class="alert alert-secondary">
        {{$pestudio->title ?? ''}}
    </div>
    <label for="scale" class="font-weight-bold text-secondary m-0">{{$list_comment['scale'] ?? ''}}</label>
    <div class="alert alert-secondary">
        {{$pestudio->scale ?? ''}}
    </div>
    <label for="profile" class="font-weight-bold text-secondary m-0">{{$list_comment['profile'] ?? ''}}</label>
    <div class="alert alert-secondary">
        {{$pestudio->profile ?? ''}}
    </div>
    <label for="color" class="font-weight-bold text-secondary m-0">{{$list_comment['color'] ?? ''}}</label>
    <div class="alert alert-secondary">
        {{$pestudio->color ?? ''}}
    </div>
    <label for="show_hr" class="font-weight-bold text-secondary m-0">{{$list_comment['show_hr'] ?? ''}}</label>
    <div class="alert alert-secondary">
        {{ ($pestudio->show_hr=='true') ? 'SI':'NO'}}
    </div>
    <label for="status_a_cualitative" class="font-weight-bold text-secondary m-0">{{$list_comment['status_a_cualitative'] ?? ''}}</label>
    <div class="alert alert-secondary">
        {{ ($pestudio->status_a_cualitative=='true') ? 'Activo':'Desactivo'}}
    </div>
    <label for="status_active" class="font-weight-bold text-secondary m-0">{{$list_comment['status_active'] ?? ''}}</label>
    <div class="alert alert-secondary">
        {{ ($pestudio->status_active=='true') ? 'Activo':'Desactivo'}}
    </div>
</div>
