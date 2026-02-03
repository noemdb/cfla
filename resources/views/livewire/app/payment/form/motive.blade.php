<div class="pb-1">
    @php $name = 'type_pay' ; $model = 'payment.'.$name; $label=$list_comment[$name] @endphp
    <x-native-select label="{{$label}}" placeholder="Seleccionar" :options="$type_pay_list" wire:model.live="{{$model}}" option-key-value flip-options="false" searchable="true" />
</div>




