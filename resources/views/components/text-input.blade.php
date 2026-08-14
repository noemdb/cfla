@props(['disabled' => false])

<input {{ $disabled ? 'disabled' : '' }} {!! $attributes->merge(['class' => 'border-gray-300 dark:border-white/10 bg-white dark:bg-slate-900 dark:text-slate-100 focus:border-emerald-500 focus:ring-emerald-500 rounded-md shadow-sm']) !!}>