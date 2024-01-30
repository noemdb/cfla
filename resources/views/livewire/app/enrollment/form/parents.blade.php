{{--

"mother_name" => null
"mother_lastname" => null
"mother_ci" => null
"mother_profession" => null
"mother_phones" => null
"mother_address" => null

--}}

<div class="pb-2">
    @php $name = 'mother_ci' ; $model = 'enrollment.'.$name; $label=$list_comment[$name] @endphp
    <x-inputs.maskable mask="##.###.###" label="{{$label}}" placeholder="{{$label}}" wire:model.live="{{$model}}" right-icon="calculator" corner-hint="Sólo números"/>
</div>

<div class="pb-2">
    @php $name = 'mother_name' ; $model = 'enrollment.'.$name; $label=$list_comment[$name] @endphp
    <x-input right-icon="table" label="{{$label}}" wire:model.live="{{$model}}"/>
</div>

<div class="pb-2">
    @php $name = 'mother_lastname' ; $model = 'enrollment.'.$name; $label=$list_comment[$name] @endphp
    <x-input right-icon="table" label="{{$label}}" wire:model.live="{{$model}}"/>
</div>

<div class="pb-2">
    @php $name = 'mother_profession' ; $model = 'enrollment.'.$name; $label=$list_comment[$name] @endphp
    <x-select label="{{$label}}" placeholder="Seleccionar" :options="$list_profession" wire:model.live="{{$model}}" option-key-value/>
</div>

<div class="pb-2">
    @php $name = 'mother_address' ; $model = 'enrollment.'.$name; $label=$list_comment[$name] @endphp
    <x-textarea right-icon="table" label="{{$label}}" wire:model.live="{{$model}}"/>
</div>

<div class="pb-2">
    @php $name = 'mother_phones' ; $model = 'enrollment.'.$name; $label=$list_comment[$name] @endphp
    <x-inputs.phone mask="(####) ###-##-##" right-icon="phone" label="{{$label}}" wire:model.live="{{$model}}" corner-hint="Sólo números"/>
</div>

<hr class="mt-4">

<div class="pb-2">
    @php $name = 'father_ci' ; $model = 'enrollment.'.$name; $label=$list_comment[$name] @endphp
    <x-inputs.maskable mask="##.###.###" label="{{$label}}" placeholder="{{$label}}" wire:model.live="{{$model}}" right-icon="calculator" corner-hint="Sólo números"/>
</div>

<div class="pb-2">
    @php $name = 'father_name' ; $model = 'enrollment.'.$name; $label=$list_comment[$name] @endphp
    <x-input right-icon="table" label="{{$label}}" wire:model.live="{{$model}}"/>
</div>

<div class="pb-2">
    @php $name = 'father_lastname' ; $model = 'enrollment.'.$name; $label=$list_comment[$name] @endphp
    <x-input right-icon="table" label="{{$label}}" wire:model.live="{{$model}}"/>
</div>

<div class="pb-2">
    @php $name = 'father_profession' ; $model = 'enrollment.'.$name; $label=$list_comment[$name] @endphp
    <x-select label="{{$label}}" placeholder="Seleccionar" :options="$list_profession" wire:model.live="{{$model}}" option-key-value/>
</div>

<div class="pb-2">
    @php $name = 'father_address' ; $model = 'enrollment.'.$name; $label=$list_comment[$name] @endphp
    <x-textarea right-icon="table" label="{{$label}}" wire:model.live="{{$model}}"/>
</div>

<div class="pb-2">
    @php $name = 'father_phones' ; $model = 'enrollment.'.$name; $label=$list_comment[$name] @endphp
    <x-inputs.phone mask="(####) ###-##-##" right-icon="phone" label="{{$label}}" wire:model.live="{{$model}}" corner-hint="Sólo números"/>
</div>
