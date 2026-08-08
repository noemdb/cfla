{{--
Avatar component for student profile
Parameters:
- $photoUrl (string|null): URL of the photo
- $initials (string|null): Initials to display when no photo
- $size (string): Tailwind size classes (default: w-12 h-12)
- $shape (string): Tailwind shape classes (default: rounded-xl)
- $alt (string|null): Alt text for image
- $showMascot (bool): Whether to show mascot fallback
- $mascotSize (string): Size for mascot (default: sm)
- $mascotEmphasis (bool): Emphasis for mascot
- $class (string): Additional classes
--}}
@props([
    'photoUrl' => null,
    'initials' => null,
    'size' => null,
    'shape' => null,
    'alt' => null,
    'showMascot' => false,
    'mascotSize' => 'sm',
    'mascotEmphasis' => false,
    'class' => '',
    'status' => null,
])
<div class="relative inline-flex items-center justify-center {{$class}}">
    @if($photoUrl)
        <img src="{{$photoUrl}}" alt="{{$alt ?? 'Avatar'}}" class="{{$size ?? 'w-12 h-12'}} {{$shape ?? 'rounded-xl'}} object-cover border-2 border-white/20 dark:border-gray-700/20">
    @elseif($initials)
        <div class="{{$size ?? 'w-12 h-12'}} {{$shape ?? 'rounded-xl'}} flex items-center justify-center bg-emerald-500/20 text-emerald-500 text-{{ str_contains($size ?? 'w-12 h-12', 'w-10') ? 'lg' : 'base' }} font-semibold">
            {{$initials}}
        </div>
    @elseif($showMascot ?? false)
        <x-lms.mascot :variant="'greet'" :size="$mascotSize ?? 'sm'" :emphasis="$mascotEmphasis ?? false" />
    @else
        <div class="{{$size ?? 'w-12 h-12'}} {{$shape ?? 'rounded-xl'}} flex items-center justify-center bg-gray-300/20 text-gray-500">
            ?
        </div>
    @endif

    @if(isset($status) && $status === 'online')
        <div class="absolute bottom-0 right-0 w-2.5 h-2.5 bg-emerald-500 rounded-full border-2 border-white dark:border-gray-800"></div>
    @elseif(isset($status) && $status === 'away')
        <div class="absolute bottom-0 right-0 w-2.5 h-2.5 bg-amber-500 rounded-full border-2 border-white dark:border-gray-800"></div>
    @endif
</div>