

<div class="padre flex flex-col sm:flex-row">
    <div class="hijo1 w-full sm:w-1/2">
        <div class="pb-2">
            @php $name = 'banco_id_1' ; $model = 'payment.'.$name; $label=$list_comment[$name] @endphp
            <x-select label="{{$label}}" placeholder="Seleccionar" :options="$list_bank" wire:model.live="{{$model}}" option-key-value />
        </div>
    </div>
    <div class="hijo2 w-full sm:w-1/2 pl-2">
        <div class="pb-2">
            @php $name = 'banco_emisor_1' ; $model = 'payment.'.$name; $label=$list_comment[$name] @endphp
            <x-select label="{{$label}}" placeholder="Seleccionar" :options="$banco_emisor_list" wire:model.live="{{$model}}" option-key-value />
        </div>
    </div>
</div>

{{-- <div class="padre flex flex-col sm:flex-row">
    <div class="hijo1 w-full sm:w-2/3">
        Contenido del hijo 1
    </div>
    <div class="hijo2 w-full sm:w-1/3 pl-1">
        Contenido del hijo 2
    </div>
</div> --}}


<div class="flex flex-col sm:flex-row">
    <div class="w-full sm:w-1/2">
        <div class="pb-2">
            @php $name = 'ammount_1' ; $model = 'payment.'.$name; $label=$list_comment[$name] @endphp
            <x-inputs.currency label="{{$label}}" prefix="Bs." thousands="." decimal="," wire:model.live="{{$model}}" />
        </div>
    </div>
    <div class="w-full sm:w-1/2 pl-2">
        <div class="pb-2">
            @php $name = 'date_transaction_1' ; $model = 'payment.'.$name; $label=$list_comment[$name] @endphp
            <x-datetime-picker parse-format="YYYY-MM-DD" display-format="DD-MM-YYYY" label="{{$label}}" placeholder="{{$label}}"
                wire:model.live="{{$model}}" :min="now()->subYearss(1)" :max="now()" without-time="false" />
        </div>
    </div>
</div>

<div class="padre flex flex-col sm:flex-row">

    <div class="hijo2 w-full sm:w-1/2 pl-1">
        <div class="pb-2">
            @php $name = 'method_pay_id_1' ; $model = 'payment.'.$name; $label=$list_comment[$name] @endphp
            <x-select label="{{$label}}" placeholder="Seleccionar" :options="$method_pay_list" wire:model.live="{{$model}}" option-key-value />
        </div>
    </div>

    <div class="hijo1 w-full sm:w-1/2 pl-2">
        <div class="pb-2">
            @php $name = 'number_i_pay_1' ; $model = 'payment.'.$name; $label=$list_comment[$name] @endphp
            <x-inputs.maskable label="{{$label}}" mask="################" placeholder="{{$label}}" wire:model.live="{{$model}}" right-icon="calculator" corner-hint="Sólo números" />
        </div>
    </div>
    
</div>

@if ($payment->method_pay_id_1 == 5 || $payment->method_pay_id_1 == 7) 
    <div class="pb-2">
        @php $name = 'phone_1' ; $model = 'payment.'.$name; $label=$list_comment[$name] @endphp
        <x-inputs.phone mask="(####) ###-##-##" right-icon="phone" label="{{$label}}" wire:model.live="{{$model}}" corner-hint="Sólo números"/>
    </div>
@endif

<div class="my-3">
    @php $name = 'image' ; $model = ''.$name; $label=$list_comment[$name] @endphp
    <label for="formFile" class="mb-2 inline-block text-neutral-700 dark:text-neutral-200">{{$label}}</label>
    <input wire:model.live="{{$model}}" class="relative m-0 block w-full min-w-0 flex-auto rounded border border-solid border-neutral-300 bg-clip-padding px-3 py-[0.32rem] text-base font-normal text-neutral-700 transition duration-300 ease-in-out file:-mx-3 file:-my-[0.32rem] file:overflow-hidden file:rounded-none file:border-0 file:border-solid file:border-inherit file:bg-neutral-100 file:px-3 file:py-[0.32rem] file:text-neutral-700 file:transition file:duration-150 file:ease-in-out file:[border-inline-end-width:1px] file:[margin-inline-end:0.75rem] hover:file:bg-neutral-200 focus:border-primary focus:text-neutral-700 focus:shadow-te-primary focus:outline-none dark:border-neutral-600 dark:text-neutral-200 dark:file:bg-neutral-700 dark:file:text-neutral-100 dark:focus:border-primary" type="file" id="formFile" />
</div>
@if ($image) 
    <div class="text-center text-xs text-gray-600 font-bold">Vista previa</div>
    <div class="flex justify-center">
        <img class="border rounded min-w-64 shadow" src="{{ $image->temporaryUrl() }}">
    </div>
@endif

<div class="pb-2">
    @php $name = 'comment' ; $model = 'payment.'.$name; $label=$list_comment[$name] @endphp
    <x-textarea right-icon="table" label="{{$label}}" wire:model.live="{{$model}}"/>
</div>