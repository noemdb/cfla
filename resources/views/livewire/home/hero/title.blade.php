<div>
    <h5
        class="text-lg sm:text-lg md:text-4xl lg:text-5xl xl:text-6xl font-extrabold text-white leading-tight shadow-md drop-shadow-lg">
        {{ $item->title ?? null }}
    </h5>
    <div
        class="text-base sm:text-lg md:text-lg lg:text-lg font-light text-emerald-100 mt-1 sm:mt-2 tracking-wide drop-shadow-md">
        {{ $item->description ?? null }}
    </div>
</div>
