{{--
Divider component for section separation
Parameters:
- $orientation (string): 'horizontal' or 'vertical' (default: horizontal)
- $class (string): Additional classes
--}}
@props([
    'orientation' => 'horizontal',
    'class' => '',
])

@if($orientation === 'horizontal')
<div class="flex h-0.5 my-6 {{$class}}">
    <div class="flex-1 bg-gray-200 dark:bg-gray-700"></div>
</div>
@else
<div class="flex w-0.5 mx-6 {{$class}}">
    <div class="flex-1 bg-gray-200 dark:bg-gray-700"></div>
</div>
@endif