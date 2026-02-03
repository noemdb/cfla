@php
        $name  = 'type_pay';
        $model = 'payment.' . $name;
        $label = $list_comment[$name];
@endphp

{{-- 
<x-select
    label="{{ $label }}"
    placeholder="Seleccionar"
    :options="$type_pay_list"
    wire:model.live="{{ $model }}"
    option-key-value
    use-native-select
/> 
--}}

<div class="pb-1">
    <x-native-select
        label="{{ $label }}"
        placeholder="Seleccionar"
        :options="$type_pay_list"
        wire:model.live="{{ $model }}"
        option-key-value
        searchable
    />
</div>
