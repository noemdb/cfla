{{-- <div class="card-header" id="headingOne"> --}}
<a class="btn btn-link {{$class_body ?? ''}}" data-toggle="collapse" href="#{{ $id ?? ''}}-bodycollapse" role="button" aria-expanded="false"
    aria-controls="{{ $id ?? ''}}-bodycollapse" style="text-decoration: none;">
    <span class="{{ $class_title ??  ''}}">{{ $title ??  ''}}</span>
</a>
{{-- </div> --}}

<div class="collapse" id="{{ $id ?? ''}}-bodycollapse">
    <div class="card card-body p-1 m-1 border-0">
        {{ $body ?? ''}}
    </div>
</div>