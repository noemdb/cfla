<div class="pb-2">
    @php $name = 'phone' ; $model = 'catchment.'.$name; $label=$list_comment[$name] @endphp
    <x-inputs.phone mask="(####) ###-##-##" right-icon="phone" label="{{$label}}" wire:model.live="{{$model}}" corner-hint="Sólo números"/>
</div>