<a 
role="button"
title="{{ $title ?? 'Crear nuevo registro' }}"
class="btn btn-{{ $class_bt }}"
href="{{ $route ?? '#' }}"
id="{{ $id ?? ''}}"
target="{{ $target ?? ''}}"
data-url="{{ $data_url ?? ''}}"
data-id="{{ $data_id ?? ''}}"
>
    <i class="{{ $icon }}"></i>
    {{ $text ?? '' }}
</a>
