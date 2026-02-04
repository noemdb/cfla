@props(['label' => null, 'placeholder' => null])

<div class="w-full">
    @if ($label)
        <label class="block text-sm font-medium text-gray-700 dark:text-gray-400 mb-1.5 ml-1">
            {{ $label }}
        </label>
    @endif
    <div class="relative group">
        <input type="date"
            {{ $attributes->merge([
                'class' => 'block w-full text-sm text-gray-200 bg-slate-900 border border-slate-700 rounded-xl p-2.5 
                                        focus:border-emerald-500/50 focus:ring-4 focus:ring-emerald-500/10 transition-all 
                                        cursor-pointer shadow-sm appearance-none',
            ]) }}
            @if ($placeholder) placeholder="{{ $placeholder }}" @endif>

        <div
            class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none text-slate-500 group-focus-within:text-emerald-500 transition-colors">
            <x-icon name="calendar" class="w-5 h-5" />
        </div>
    </div>
</div>
