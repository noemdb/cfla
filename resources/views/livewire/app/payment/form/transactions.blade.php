<div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
    <div>
        @php
            $name = 'ammount_1';
            $model = 'payment.' . $name;
            $label = $list_comment[$name];
        @endphp
        <x-currency label="{{ $label }}" prefix="Bs." thousands="." decimal=","
            wire:model.live="{{ $model }}" class="" />
    </div>
    <div>
        @php
            $name = 'date_transaction_1';
            $model = 'payment.' . $name;
            $label = $list_comment[$name];
        @endphp
        {{-- <x-datetime-picker position="top" parse-format="YYYY-MM-DD" display-format="DD-MM-YYYY" label="{{ $label }}"
            placeholder="{{ $label }}" wire:model.live="{{ $model }}" :min="now()->subYears(1)" :max="now()"
            without-time="false" class="" /> --}}

        <x-date label="{{ $label }}" placeholder="{{ $label }}" wire:model.live="{{ $model }}"
            :min="now()->subYears(1)" :max="now()" />
    </div>
</div>

<div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
    <div>
        @php
            $name = 'banco_id_1';
            $model = 'payment.' . $name;
            $label = $list_comment[$name];
        @endphp
        <x-native-select label="{{ $label }}" placeholder="Seleccionar" :options="$list_bank"
            wire:model.live="{{ $model }}" option-key-value />
    </div>
    <div>
        @php
            $name = 'banco_emisor_1';
            $model = 'payment.' . $name;
            $label = $list_comment[$name];
        @endphp
        <x-native-select label="{{ $label }}" placeholder="Seleccionar" :options="$banco_emisor_list"
            wire:model.live="{{ $model }}" option-key-value class="" />
    </div>
</div>

<div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
    <div>
        @php
            $name = 'method_pay_id_1';
            $model = 'payment.' . $name;
            $label = $list_comment[$name];
        @endphp
        <x-native-select label="{{ $label }}" placeholder="Seleccionar" :options="$method_pay_list"
            wire:model.live="{{ $model }}" option-key-value class="" />
    </div>
    <div>
        @php
            $name = 'number_i_pay_1';
            $model = 'payment.' . $name;
            $label = $list_comment[$name];
        @endphp
        <x-maskable label="{{ $label }}" mask="################" placeholder="{{ $label }}"
            wire:model.live="{{ $model }}" right-icon="calculator" corner-hint="Sólo números" class="" />
    </div>
</div>

@if ($payment->method_pay_id_1 == 5 || $payment->method_pay_id_1 == 7)
    <div class="pb-2">
        @php
            $name = 'phone_1';
            $model = 'payment.' . $name;
            $label = $list_comment[$name];
        @endphp
        <x-phone mask="(####) ###-##-##" right-icon="phone" label="{{ $label }}"
            wire:model.live="{{ $model }}" corner-hint="Sólo números" />
    </div>
@endif

<div class="mb-5">
    @php
        $name = 'image';
        $model = '' . $name;
        $label = $list_comment[$name];
    @endphp
    <label for="formFile"
        class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">{{ $label }}</label>
    <div class="relative group mt-1">
        <input wire:model.live="{{ $model }}"
            class="block w-full text-sm text-slate-400
            file:mr-4 file:py-2.5 file:px-5
            file:rounded-xl file:border-0
            file:text-xs file:font-bold file:uppercase
            file:bg-emerald-600 file:text-white
            hover:file:bg-emerald-500
            dark:bg-slate-900/50 dark:border-slate-800 rounded-xl border border-dashed border-slate-700 p-3 transition-all cursor-pointer shadow-inner"
            type="file" id="formFile" />
    </div>
</div>
@if ($image)
    <div class="text-center text-xs text-gray-600 font-bold">Vista previa</div>
    <div class="flex justify-center">
        <img class="border rounded min-w-64 shadow" src="{{ $image->temporaryUrl() }}">
    </div>
@endif

<div class="pb-2">
    @php
        $name = 'comment';
        $model = 'payment.' . $name;
        $label = $list_comment[$name];
    @endphp
    <x-textarea right-icon="table" label="{{ $label }}" wire:model.live="{{ $model }}"
        class="border border-slate-700 p-2" />
</div>
