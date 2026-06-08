<a
    role="button"
    title="{{ $title ?? 'Crear nuevo registro' }}"
    class="btn btn-{{ $class_bt ?? 'primary' }} {{ $disabled ?? ''}}"
    href="{{ $route ?? '#' }}"
    id="{{ $id ?? ''}}"
    target="{{ $target ?? ''}}"
    data-url="{{ $data_url ?? ''}}"
    data-id="{{ $data_id ?? ''}}"
>
    <i class="{{ $icon ?? $icon_menus['nuevo']}}"></i>
    {{ $text ?? '' }}
</a>
