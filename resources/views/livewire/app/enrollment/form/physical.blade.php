{{--
"age" => 0
"blood_type" => "A"
"weight" => 0
"height" => 0
"laterality" => "IZQUIERDA"
"order_born" => "1"
"group_family" => 0
"status_brother" => "true"
--}}

<div class="pb-2">
    @php $name = 'blood_type' ; $model = 'enrollment.'.$name; $label=$list_comment[$name] @endphp
    <x-select label="{{$label}}" placeholder="Seleccionar" :options="$list_blood_type" wire:model.live="{{$model}}" option-key-value/>
</div>

<div class="pb-2">
    @php $name = 'laterality' ; $model = 'enrollment.'.$name; $label=$list_comment[$name] @endphp
    <x-select label="{{$label}}" placeholder="Seleccionar" :options="$list_laterality" wire:model.live="{{$model}}" option-key-value/>
</div>

<div class="pb-2">
    @php $name = 'age' ; $model = 'enrollment.'.$name; $label=$list_comment[$name] @endphp
    <x-inputs.maskable label="{{$label}}" mask="##" placeholder="{{$label}}" wire:model.live="{{$model}}" right-icon="calculator" corner-hint="Sólo números" />
</div>

<div class="pb-2">
    @php $name = 'weight' ; $model = 'enrollment.'.$name; $label=$list_comment[$name] @endphp
    <x-inputs.maskable label="{{$label}}" mask="##" placeholder="{{$label}}" wire:model.live="{{$model}}" right-icon="calculator" corner-hint="Sólo números" />
</div>

<div class="pb-2">
    @php $name = 'height' ; $model = 'enrollment.'.$name; $label=$list_comment[$name] @endphp
    <x-inputs.maskable label="{{$label}}" mask="###" placeholder="{{$label}}" wire:model.live="{{$model}}" right-icon="calculator" corner-hint="Sólo números" />
</div>

<div class="pb-2">
    @php $name = 'order_born' ; $model = 'enrollment.'.$name; $label=$list_comment[$name] @endphp
    <x-inputs.maskable label="{{$label}}" mask="##" placeholder="{{$label}}" wire:model.live="{{$model}}" right-icon="calculator" corner-hint="Sólo números" />
</div>

<div class="pb-2">
    @php $name = 'group_family' ; $model = 'enrollment.'.$name; $label=$list_comment[$name] @endphp
    <x-inputs.maskable label="{{$label}}" mask="##" placeholder="{{$label}}" wire:model.live="{{$model}}" right-icon="calculator" corner-hint="Sólo números" />
</div>

<div class="pb-2">
    @php $name = 'status_brother' ; $model = 'enrollment.'.$name; $label=$list_comment[$name] @endphp
    <x-toggle lg label="{{$label}}" wire:model.live="{{$model}}" />
</div>

<div class="p-4 border rounded-lg shadow-lg">

    <div class="mb-3">
        @php $name = 'image' ; $model = ''.$name; $label=$list_comment[$name] @endphp
        <label for="formFile" class="mb-2 inline-block text-neutral-700 dark:text-neutral-200">{{$label}}</label>
        <input wire:model.live="{{$model}}" class="relative m-0 block w-full min-w-0 flex-auto rounded border border-solid border-neutral-300 bg-clip-padding px-3 py-[0.32rem] text-base font-normal text-neutral-700 transition duration-300 ease-in-out file:-mx-3 file:-my-[0.32rem] file:overflow-hidden file:rounded-none file:border-0 file:border-solid file:border-inherit file:bg-neutral-100 file:px-3 file:py-[0.32rem] file:text-neutral-700 file:transition file:duration-150 file:ease-in-out file:[border-inline-end-width:1px] file:[margin-inline-end:0.75rem] hover:file:bg-neutral-200 focus:border-primary focus:text-neutral-700 focus:shadow-te-primary focus:outline-none dark:border-neutral-600 dark:text-neutral-200 dark:file:bg-neutral-700 dark:file:text-neutral-100 dark:focus:border-primary" type="file" id="formFile" />
    </div>

    @if ($image) 
        <div class="text-center text-xs text-gray-600 font-bold">Vista previa</div>
        <div class="flex justify-center">
            <img class="border rounded min-w-48 shadow" src="{{ $image->temporaryUrl() }}">
        </div>
    @else

    <div class="text-center text-xs text-gray-600 font-bold">Imagen de ejemplo</div>
        <div class="flex justify-center">
            <img class="border rounded min-w-48 shadow" src="{{ asset('image/avatar/person.png') }}">
        </div>
    @endif
</div>