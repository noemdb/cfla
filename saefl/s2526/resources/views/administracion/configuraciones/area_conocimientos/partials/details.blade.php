<div class=" d-block">
    <label for="pestudio_id" class="font-weight-bold text-secondary m-0">{{$list_comment_area['pestudio_id'] ?? ''}}</label>
    <div class="alert alert-secondary">
        {{ $area_conocimiento->pestudio->name ?? '' }}
    </div>
    <label for="name" class="font-weight-bold text-secondary m-0">{{$list_comment_area['name'] ?? ''}}</label>
    <div class="alert alert-secondary">
        {{ $area_conocimiento->name ?? '' }}
    </div>
    <label for="code" class="font-weight-bold text-secondary m-0">{{$list_comment_area['code'] ?? ''}}</label>
    <div class="alert alert-secondary">
        {{ $area_conocimiento->code ?? '' }}
    </div>
    <label for="code_sm" class="font-weight-bold text-secondary m-0">{{$list_comment_area['code_sm'] ?? ''}}</label>
    <div class="alert alert-secondary">
        {{ $area_conocimiento->code_sm ?? '' }}
    </div>
    <label for="description" class="font-weight-bold text-secondary m-0">{{$list_comment_area['description'] ?? ''}}</label>
    <div class="alert alert-secondary">
        {{ $area_conocimiento->description ?? '' }}
    </div>
    <label for="observations" class="font-weight-bold text-secondary m-0">{{$list_comment_area['observations'] ?? ''}}</label>
    <div class="alert alert-secondary">
        {{ $area_conocimiento->observations ?? '' }}
    </div>
    <label for="order" class="font-weight-bold text-secondary m-0">{{$list_comment_area['order'] ?? ''}}</label>
    <div class="alert alert-secondary">
        {{ $area_conocimiento->order ?? '' }}
    </div>
    <label for="enable_academic_index" class="font-weight-bold text-secondary m-0">{{$list_comment_area['enable_academic_index'] ?? ''}}</label>
    <div class="alert alert-secondary">
        {{ ($area_conocimiento->enable_academic_index=='true') ? 'SI':'NO' }}
    </div>
</div>
{{--
const COLUMN_COMMENTS = [
'pestudio_id' => 'Plan Estudio',
'name' => 'Nombre',
'code' => 'Código',
'code_sm' => 'Abreviatura',
'description' => 'Descripción',
'observations' => 'Observaciones',
'order' => 'Número de orden de presentación',
'enable_academic_index' => 'Tomada en cuenta para índice o promedio académico',
];
--}}
