{{--
"ci_representant" => "12079224"
"name_representant" => "ANA YOMAIRA SANCHEZ ORDOÑEZ"
"relationship" => "Madre"
"profession_representant" => ""
"phone_representant" => "04125081606"
"email_representant" => "anayso1306@gmail.com"
"recommended_by" => null
--}}

<div class="pb-2">
    @php $name = 'ci_representant' ; $model = 'enrollment.'.$name; $label=$list_comment[$name] @endphp
    <x-inputs.maskable mask="##.###.###" label="{{$label}}" placeholder="{{$label}}" wire:model.live="{{$model}}" right-icon="calculator" corner-hint="Sólo números"/>
</div>

<div class="pb-2">
    @php $name = 'name_representant' ; $model = 'enrollment.'.$name; $label=$list_comment[$name] @endphp
    <x-input right-icon="table" label="{{$label}}" wire:model.live="{{$model}}"/>
</div>

<div class="pb-2">
    @php $name = 'phone_representant' ; $model = 'enrollment.'.$name; $label=$list_comment[$name] @endphp
    <x-inputs.phone mask="(####) ###-##-##" right-icon="phone" label="{{$label}}" wire:model.live="{{$model}}"/>
</div>

<div class="pb-2">
    @php $name = 'cellphone_representant' ; $model = 'enrollment.'.$name; $label=$list_comment[$name] @endphp
    <x-inputs.phone mask="(####) ###-##-##" right-icon="table" label="{{$label}}" wire:model.live="{{$model}}" corner-hint="Sólo números"/>
</div>

<div class="pb-2">
    @php $name = 'relationship' ; $model = 'enrollment.'.$name; $label=$list_comment[$name] @endphp
    <x-select label="{{$label}}" placeholder="Seleccionar" :options="$list_relationship" wire:model.live="{{$model}}" option-key-value/>
</div>

<div class="pb-2">
    @php $name = 'profession_representant' ; $model = 'enrollment.'.$name; $label=$list_comment[$name] @endphp
    <x-select label="{{$label}}" placeholder="Seleccionar" :options="$list_profession" wire:model.live="{{$model}}" option-key-value/>
</div>

<div class="pb-2">
    @php $name = 'email_representant' ; $model = 'enrollment.'.$name; $label=$list_comment[$name] @endphp
    <x-input label="{{$label}}" wire:model.live="{{$model}}" suffix="@mail.com" class="pr-28"/>
</div>

<div class="pb-2">
    @php $name = 'recommended_by' ; $model = 'enrollment.'.$name; $label=$list_comment[$name] @endphp
    <x-input right-icon="table" label="{{$label}}" wire:model.live="{{$model}}"/>
</div>
