<div class="btn-group-vertical" role="group" aria-label="Basic example">
    @foreach ($errors as $error)
        <span class="btn badge badge-{{ $error['class'] ?? 'secondary' }}" title="{{ $error['messenge'] ?? '' }}">
            {{ $error['code'] ?? '' }}
        </span>
    @endforeach
</div>