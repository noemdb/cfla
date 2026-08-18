@foreach ($groups as $group)
    @if (!empty($group['mega_menu']))
        <x-menu.mega-menu-mobile :group="$group" />
    @elseif (count($group['items'] ?? []))
        <x-menu.group-mobile :group="$group" />
    @endif
@endforeach