<div class="pb-2">
    @php $name = 'type_pay' ; $model = 'payment.'.$name; $label=$list_comment[$name] @endphp
    <x-select label="{{$label}}" placeholder="Seleccionar" :options="$type_pay_list" wire:model.live="{{$model}}" option-key-value/>
</div>

<div class="pb-2">
    @php $name = 'phone_1' ; $model = 'payment.'.$name; $label=$list_comment[$name] @endphp
    <x-inputs.phone mask="(####) ###-##-##" right-icon="phone" label="{{$label}}" wire:model.live="{{$model}}" corner-hint="Sólo números"/>
</div>

<div class="pb-2">
    @php $name = 'comment' ; $model = 'payment.'.$name; $label=$list_comment[$name] @endphp
    <x-textarea right-icon="table" label="{{$label}}" wire:model.live="{{$model}}"/>
</div>
